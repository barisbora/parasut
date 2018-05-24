<?php namespace barisbora\Parasut\Methods;

use BadMethodCallException;
use barisbora\Parasut\Exceptions\CompanyNotSelectedException;
use barisbora\Parasut\Parameters\Query;
use barisbora\Parasut\Parasut;
use barisbora\Parasut\Tools\Helper;

abstract class Eloquent
{

    use Helper;

    protected $parasut;

    protected $query;

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

        $this->query = new Query();
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

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call( $name, $arguments )
    {

        $query = $this->query::methods();

        if ( in_array( $name, $query ) ) {

            $this->query->{$name}( ...$arguments );

            return $this;

        }

        throw new BadMethodCallException();

    }
}
