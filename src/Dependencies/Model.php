<?php namespace barisbora\Parasut\Dependencies;

use Carbon\Carbon;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;

class Model implements JsonSerializable, Jsonable
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $relationships = [];

    /**
     * @param      $data
     * @param null $includes
     * @return static|\Illuminate\Support\Collection
     */
    public static function build( $data, $includes = null )
    {
        if ( is_array( @$data->data ) ) return self::collect( $data );

        return new static( isset( $data->data ) ? $data->data : $data, $includes ? $includes : @$data->included );
    }

    /**
     * @param      $data
     * @return \Illuminate\Support\Collection
     */
    private static function collect( $data )
    {
        $collection = collect();

        foreach ( $data->data as $model ) {
            $collection->push( new static( $model, @$data->included ) );
        }

        return $collection;
    }

    /**
     * Model constructor.
     *
     * @param      $data
     * @param null $includes
     */
    private function __construct( $data, $includes = null )
    {

        $this->id = (int) $data->id;
        $this->type = $data->type;
        $this->attributes = (array) $data->attributes;

        unset( $this->attributes[ 'created_at' ], $this->attributes[ 'updated_at' ] );

        if ( isset( $data->attributes->created_at ) ) $this->attributes[ 'created_at' ] = Carbon::createFromTimeString( $data->attributes->created_at );
        if ( isset( $data->attributes->updated_at ) ) $this->attributes[ 'updated_at' ] = Carbon::createFromTimeString( $data->attributes->updated_at );

        $includes = is_array( $includes ) || is_null( $includes ) ? collect( $includes ) : $includes;

        if ( ! isset( $data->relationships ) ) return $this;

        collect( $data->relationships )->each( function ( $relationship, $key ) use ( $includes ) {

            $this->relationships[ $key ] = null;

            if ( $includes->count() ) {

                if ( isset( $relationship->data ) ) {

                    if ( is_array( $relationship->data ) ) {

                        foreach ( $relationship->data as $item ) {

                            $model = $this->extractRelationship( $item, $includes );

                            if ( $model ) $this->relationships[ $key ][] = $model;

                        }

                    } else {

                        $model = $this->extractRelationship( $relationship->data, $includes );

                        if ( $model ) $this->relationships[ $key ] = $model;

                    }

                }

            }

        } );

    }

    /**
     * @param $item
     * @param $includes
     * @return \barisbora\Parasut\Dependencies\Model|null
     */
    private function extractRelationship( $item, $includes )
    {

        if ( isset( $item->id ) && isset( $item->type ) ) {

            $find = $includes->where( 'type', $item->type )->where( 'id', $item->id )->first();

            if ( $find ) return Model::build( $find, $includes );

        }

        return null;

    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_merge( [ 'id' => $this->id, 'type' => $this->type ], $this->attributes, $this->relationships );;
    }

    /**
     * @param int $options
     * @return string
     * @throws \Exception
     */
    public function toJson( $options = 0 )
    {
        $json = json_encode( $this->jsonSerialize(), $options );

        if ( JSON_ERROR_NONE !== json_last_error() ) throw new \Exception( json_last_error_msg() );

        return $json;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get( $name )
    {
        if ( isset( $this->relationships[ $name ] ) ) return $this->relationships[ $name ];

        if ( isset( $this->attributes[ $name ] ) ) return $this->attributes[ $name ];

        if ( in_array( $name, [ 'id', 'type' ] ) ) return $this->{$name};

        return null;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
