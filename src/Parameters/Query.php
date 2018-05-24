<?php namespace barisbora\Parasut\Parameters;

class Query
{

    public function __construct()
    {
        $this->includes = collect();
    }

    /**
     * @var array
     */
    protected $page = [
        'size'   => 15,
        'number' => 1,
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
     */
    public function size( $size )
    {
        $size = (int) $size;

        $this->page[ 'size' ] = (int) $size;

        return $this;
    }

    /**
     * @param $page
     * @return $this
     */
    public function page( $page )
    {
        $this->page[ 'number' ] = (int) $page;

        return $this;
    }

    /**
     * @param        $column
     * @param string $type
     * @return $this
     */
    public function orderBy( $column, $type = 'asc' )
    {
        $this->sort = $type == 'desc' ? ( '-' . $column ) : ( $column );

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
     * @param array $rules
     * @return $this
     */
    public function where( array $rules )
    {

        foreach ( $rules as $rule => $value ) {

            $this->filter[ $rule ] = $value;

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

        unset( $data[ 'includes' ] );

        return array_merge( $options, [ 'form_params' => $data ] );
    }

    public static function methods()
    {
        $methods = get_class_methods( self::class );

        return array_diff( $methods, [ '__construct', 'query', 'methods' ] );
    }
}
