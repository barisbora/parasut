<?php namespace barisbora\Parasut\Dependencies;

use Carbon\Carbon;

class User
{

    private $data;

    private $id;

    private $name;

    private $email;

    private $confirmed;

    private $phone;

    private $title;

    private $avatar;

    private $companies = [];

    public function __construct( $data )
    {

        $this->data = $data;
        $this->id = (int) $data->data->id;
        $this->name = $data->data->attributes->name;
        $this->email = $data->data->attributes->email;
        $this->confirmed = (boolean) $data->data->attributes->is_confirmed;

        $includes = collect( $data->included );

        # Profile
        $profile = $includes->where( 'type', 'profiles' )->first();
        if ( $profile ) {
            $this->phone = $profile->attributes->phone;
            $this->title = $profile->attributes->job_title;
            $this->avatar = $profile->attributes->avatar->url;
        }

        # Companies
        $this->companies = $includes->where( 'type', 'companies' )->transform( function ( $company ) {

            return new Company( $company );

        } )->values();

        $companies = $includes->where( 'type', 'companies' );

        foreach ( $companies as $company ) {

            $role = $includes->filter( function ( $include ) use ( $company ) {

                return $include->type == 'user_roles' && $include->relationships->company->data->id == $company->id;

            } )->first();

            $this->companies->where( 'id', $company->id )->first()->setRole( new Permission( $role ) );

            if ( $company->relationships->address ) {

                $address = $includes->filter( function ( $include ) use ( $company ) {

                    return $include->type == 'addresses' && $include->id == $company->relationships->address->data->id;

                } )->first();

                $this->companies->where( 'id', $company->id )->first()->setAddress( new Address( $address ) );

            }

            if ( $company->relationships->owner ) {

                $owner = $includes->filter( function ( $include ) use ( $company ) {

                    return $include->type == 'users' && $include->id == $company->relationships->owner->data->id;

                } )->first();

                $this->companies->where( 'id', $company->id )->first()->setOwner( new Owner( $owner ) );

            }

        }

        /*
        # Role
        $role = $includes->where( 'type', 'user_roles' )->first();
        if ( $role ) {
            $this->sales_invoices = Role::make( $role->attributes->sales_invoices );
            $this->expenditures = Role::make( $role->attributes->expenditures );
            $this->expenditures_own = Role::make( $role->attributes->own_expenditures );
            $this->employees = Role::make( $role->attributes->employees );
            $this->accounts = Role::make( $role->attributes->accounts );
            $this->settings = Role::make( $role->attributes->settings );
        }

        $this->createdAt = Carbon::createFromTimeString( $data->data->attributes->created_at );
        $this->updatedAt = Carbon::createFromTimeString( $data->data->attributes->updated_at );*/

    }
}
