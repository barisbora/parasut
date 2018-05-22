<?php namespace barisbora\Parasut\Methods;

use barisbora\Parasut\Exceptions\CompanyNotSelectedException;
use barisbora\Parasut\Parasut;
use barisbora\Parasut\Tools\Helper;

abstract class Model
{

    use Helper;

    protected $parasut;

    public $id;

    /**
     * ParasutMethod constructor.
     *
     * @param \barisbora\Parasut\Parasut $parasut
     * @throws \barisbora\Parasut\Exceptions\CompanyNotSelectedException
     */
    public function __construct( Parasut $parasut = null )
    {
        if ( $parasut ) {
            if ( is_null( $parasut->company() ) ) throw new CompanyNotSelectedException( 'User have more than one company. First select the company to be traded' );

            $this->parasut = $parasut;
        }
    }

    /**
     * @param        $endpoint
     * @param string $method
     * @param array  $options
     * @return string
     */
    protected function endpoint( $endpoint, $method = 'get', $options = [] )
    {
        return $this->parseResponse( $this->parasut->client->{$method}( $this->parasut->version . '/' . $this->parasut->company()->id . '/' . $endpoint, $options ) );
    }

    protected static function make( $response, $class )
    {

        $includes = collect( $response->included );

        if ( is_array( $response->data ) ) {

            $collection = collect();

            foreach ( $response->data as $response ) {

                $class = ( new $class );

                $class->id = (int) $response->id;

                foreach ( $response->attributes as $attr => $value ) {
                    $class->{$attr} = $value;
                }

                $collection->push( $class );

            }

            return $collection;

        } else {

            $class = ( new $class );

            $class->id = (int) $response->data->id;

            $relationships = $response->data->relationships;

            foreach ( $response->data->attributes as $attr => $value ) {
                $class->{$attr} = $value;
            }

            foreach ( $relationships as $key => $relationship ) {

                if ( isset( $relationship->data ) && ! is_null( $relationship->data ) ) {

                    $loads = $relationship->data;

                    if ( is_array( $loads ) ) {

                        $collected = collect();

                        foreach ( $loads as $load ) {

                            if ( isset( $load->id ) && isset( $load->type ) ) {

                                $include = $includes->where( 'type', $load->type )->where( 'id', $load->id )->first();

                                $model = [
                                    'id' => (int) $include->id
                                ];

                                foreach ( $include->attributes as $subKey => $val )
                                {
                                    $model[$subKey] = $val;
                                }

                                $collected->push((object)$model);

                            }

                        }

                        $class->{$key} = $collected;

                    } else {

                        if ( isset( $loads->id ) && isset( $loads->type ) ) {

                            $include = $includes->where( 'type', $loads->type )->where( 'id', $loads->id )->first();

                            $model = [
                                'id' => (int) $include->id
                            ];

                            foreach ( $include->attributes as $subKey => $val )
                            {
                                $model[$subKey] = $val;
                            }

                            $class->{$key} = (object)$model;

                        }

                    }


                }
            }

            return $class;

        }
    }
}
