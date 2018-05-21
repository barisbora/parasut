<?php namespace barisbora\Parasut\Dependencies;

use Carbon\Carbon;

class Address
{

    private $id;

    private $name;

    private $address;

    private $phone;

    private $fax;

    private $created_at;

    private $updated_at;

    public function __construct( $data )
    {

        $this->id = (int) $data->id;
        $this->name = $data->attributes->name;
        $this->address = $data->attributes->address;
        $this->phone = $data->attributes->fax;
        $this->fax = $data->attributes->phone;
        $this->created_at = Carbon::createFromTimeString( $data->attributes->created_at );
        $this->updated_at = Carbon::createFromTimeString( $data->attributes->updated_at );

    }
}
