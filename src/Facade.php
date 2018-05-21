<?php namespace barisbora\Parasut;

class Facade extends \Illuminate\Support\Facades\Facade
{

    protected static function getFacadeAccessor()
    {
        return Parasut::class;
    }

}
