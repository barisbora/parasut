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

        $this->createdAt = Carbon::createFromTimeString( $data->data->attributes->created_at );
        $this->updatedAt = Carbon::createFromTimeString( $data->data->attributes->updated_at );

    }
}
