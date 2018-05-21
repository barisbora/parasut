<?php

namespace barisbora\Parasut;

use barisbora\Parasut\Dependencies\User;
use barisbora\Parasut\Exceptions\AuthorizationException;
use barisbora\Parasut\Exceptions\ConfigFileNotExistsOrProper;
use barisbora\Parasut\Exceptions\RequestException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Cache;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

class Parasut
{

    private $accessToken;

    private $refreshToken;

    private $tokenLife;

    private $client;

    private $version = 'v4/';

    public static function boot()
    {
        return new static();
    }

    /**
     * Parasut constructor.
     *
     * @throws \barisbora\Parasut\Exceptions\ConfigFileNotExistsOrProper
     */
    private function __construct()
    {

        $this->checkConfigFileIsProper();

        $handler = new CurlHandler();

        $stack = HandlerStack::create( $handler );

        $stack->push( $this->authorizationHeader() );

        $stack->push( function ( $handler ) {

            return function ( $request, array $options ) use ( $handler ) {

                if ( empty( $options[ 'http_errors' ] ) ) {

                    return $handler( $request, $options );

                }

                return $handler( $request, $options )->then( function ( $response ) use ( $request, $handler ) {

                    $code = $response->getStatusCode();

                    if ( $code < 400 ) {
                        return $response;
                    }

                    if ( $code == 401 ) throw new AuthorizationException( 'Unauthorized' );

                    $body = json_decode( $response->getBody()->getContents() );

                    throw new RequestException( $body->error_description );

                } );

            };

        }, 'http_errors' );

        $this->client = new Client( [
            'base_uri' => 'https://api.parasut.com/',
            'handler'  => $stack,
        ] );

        if ( Cache::has( 'parasut_credentials' ) ) {

            $this->setCredentials( Cache::get( 'parasut_credentials' ) );

        }

    }

    /**
     * @return static
     * @throws \barisbora\Parasut\Exceptions\ConfigFileNotExistsOrProper
     */
    public static function refresh()
    {
        Cache::forget( 'parasut_credentials' );

        return new static();
    }

    /**
     * @param bool $remember
     * @return $this
     */
    public function connect( $remember = true )
    {

        if ( ! $this->isConnected() ) {

            $getCredentials = function () {

                $body = $this->parseResponse( $this->client->post( 'oauth/token', [
                    'form_params' => [
                        //'client_id'    => config( 'parasut.client-id' ),
                        'username'     => config( 'parasut.username' ),
                        'password'     => config( 'parasut.password' ),
                        'grant_type'   => 'password',
                        'redirect_uri' => 'ietf:wg:oauth:2.0:oob',
                    ],
                ] ) );

                $body->expires_in = Carbon::now()->addSeconds( $body->expires_in );

                return $body;

            };

            if ( $remember ) {

                return $this->setCredentials( Cache::remember( 'parasut_credentials', 110, $getCredentials ) );

            }

            return $this->setCredentials( $getCredentials() );

        }

        return $this;

    }

    /**
     * @return $this|\barisbora\Parasut\Parasut
     */
    public function refreshToken()
    {

        if ( ! $this->isConnected() || is_null( $this->refreshToken ) ) return $this->connect();

        $response = $this->client->post( 'oauth/token', [
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'client_id'     => config( 'parasut.client-id' ),
                'client_secret' => config( 'parasut.client-secret' ),
                'refresh_token' => $this->refreshToken,
            ],
        ] );

        $credentials = $this->parseResponse( $response );

        $credentials->expires_in = Carbon::now()->addSeconds( $credentials->expires_in );

        $this->setCredentials( $credentials, true );

        return $this;

    }

    /**
     * @return mixed
     */
    public function me()
    {

        $this->connect();

        return new User( $this->parseResponse( $this->client->get( $this->version . 'me' ) ) );

    }

    /**
     * @param      $credentials
     * @param bool $remember
     * @return $this
     */
    private function setCredentials( $credentials, $remember = false )
    {

        if ( $remember ) Cache::remember( 'parasut_credentials', 110, $credentials );

        $this->accessToken = $credentials->access_token;
        $this->refreshToken = $credentials->refresh_token;
        $this->tokenLife = $credentials->expires_in;

        return $this;

    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->accessToken && $this->tokenLife->greaterThan( Carbon::now() );
    }

    /**
     * @param $response
     * @return mixed
     */
    private function parseResponse( $response )
    {
        return json_decode( $response->getBody()->getContents() );
    }

    /**
     * @throws \barisbora\Parasut\Exceptions\ConfigFileNotExistsOrProper
     */
    private function checkConfigFileIsProper()
    {
        if ( ! ! ! config( 'parasut.username' ) || ! ! ! config( 'parasut.password' ) || ! ! ! config( 'parasut.client-id' ) || ! ! ! config( 'parasut.client-secret' ) ) throw new ConfigFileNotExistsOrProper( 'Paraşüt config file does not exists or config/parasut.php is not proper' );
    }

    private function authorizationHeader()
    {
        return function ( $handler ) {
            return function ( $request, array $options ) use ( $handler ) {

                if ( $this->accessToken ) {
                    $request = $request->withHeader( 'Authorization', 'Bearer ' . $this->accessToken );
                }

                return $handler( $request, $options );
            };
        };
    }
}
