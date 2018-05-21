<?php namespace barisbora\Parasut\Dependencies;

use Carbon\Carbon;

class Permission
{

    private $id;

    private $sales_invoices;

    private $expenditures;

    private $expenditures_own;

    private $employees;

    private $accounts;

    private $settings;

    private $created_at;

    private $updated_at;

    public function __construct( $data )
    {
        $this->id = (int) $data->id;
        $this->sales_invoices = Role::make( $data->attributes->sales_invoices );
        $this->expenditures = Role::make( $data->attributes->expenditures );
        $this->expenditures_own = Role::make( $data->attributes->own_expenditures );
        $this->employees = Role::make( $data->attributes->employees );
        $this->accounts = Role::make( $data->attributes->accounts );
        $this->settings = Role::make( $data->attributes->settings );
        $this->created_at = Carbon::createFromTimeString( $data->attributes->created_at );
        $this->updated_at = Carbon::createFromTimeString( $data->attributes->updated_at );
    }
}
