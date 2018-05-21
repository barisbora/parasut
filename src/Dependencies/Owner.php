<?php namespace barisbora\Parasut\Dependencies;

use Carbon\Carbon;

class Owner
{

    private $id;

    private $name;

    private $email;

    private $confirmed;

    private $created_at;

    private $updated_at;

    public function __construct( $data )
    {

        $this->id = (int) $data->id;
        $this->name = $data->attributes->name;
        $this->email = $data->attributes->email;
        $this->confirmed = (bool) $data->attributes->is_confirmed;
        $this->created_at = Carbon::createFromTimeString( $data->attributes->created_at );
        $this->updated_at = Carbon::createFromTimeString( $data->attributes->updated_at );

    }
}
