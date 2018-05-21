<?php namespace barisbora\Parasut\Dependencies;

class Role
{

    private $read = false;

    private $write = false;

    public static function make( $role )
    {
        return new static( $role );
    }

    private function __construct( $role )
    {
        if ( $role == 'rw' ) {
            $this->read = true;
            $this->write = true;
        }

        if ( $role == 'ro' ) {
            $this->read = true;
        }
    }
}
