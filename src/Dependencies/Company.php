<?php namespace barisbora\Parasut\Dependencies;

use Carbon\Carbon;

class Company
{

    public $id;
    public $name;
    public $legalName;
    public $url;
    public $city;
    public $district;
    public $occupation;
    public $tax_number;
    public $tax_office;
    public $mersis;
    public $owner;
    public $address;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct( $data )
    {
        $this->id = (int) $data->id;
        $this->name = $data->attributes->name;
        $this->legalName = $data->attributes->legal_name;
        $this->url = $data->attributes->app_url;
        $this->city = $data->attributes->city;
        $this->district = $data->attributes->district;
        $this->occupation = $data->attributes->occupation_field;
        $this->tax_number = $data->attributes->tax_number;
        $this->tax_office = $data->attributes->tax_office;
        $this->mersis = $data->attributes->mersis_no;
        $this->created_at = Carbon::createFromTimeString( $data->attributes->created_at );
        $this->updated_at = Carbon::createFromTimeString( $data->attributes->updated_at );
    }

    public function setOwner( $user )
    {
        $this->owner = $user;

        return $this;
    }

    public function setAddress( $address )
    {
        $this->address = $address;

        return $this;
    }

    public function setRole( $role )
    {
        $this->role = $role;

        return $this;
    }

}
