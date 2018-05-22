<?php namespace barisbora\Parasut\Parameters;

use barisbora\Parasut\Exceptions\InvalidPageSizeException;

abstract class Parameter
{

    public function __construct()
    {
        $this->includes = collect();
    }

    /**
     * @var array
     */
    protected $page = [
        'size' => 15,
        'page' => 1,
    ];

    /**
     * @var string
     */
    protected $sort = 'id';

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * @param $size
     * @return $this
     * @throws \barisbora\Parasut\Exceptions\InvalidPageSizeException
     */
    public function size( $size )
    {
        $size = (int) $size;

        if ( $size < 3 || $size > 25 ) throw new InvalidPageSizeException( 'Page size must be between [3 - 25]' );

        $this->page[ 'size' ] = (int) $size;

        return $this;
    }

    /**
     * @param $page
     * @return $this
     */
    public function page( $page )
    {
        $this->page[ 'page' ] = (int) $page;

        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function sort( $column )
    {
        $this->sort = $column;

        return $this;
    }

    /**
     * @param $includes
     * @return $this
     */
    public function with( ...$includes )
    {
        if ( is_array( $includes[ 0 ] ) ) $includes = $includes[ 0 ];

        foreach ( $includes as $include ) {
            $this->includes->push( $include );
        }

        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    public function query( $options = [] )
    {
        $options = (array) $options;

        $data = get_object_vars( $this );

        $data[ 'include' ] = $data[ 'includes' ]->implode( ',' );

        return array_merge( $options, [ 'form_params' => $data ] );
    }
}
