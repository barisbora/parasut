<?php

namespace barisbora\Parasut;

use Illuminate\Support\ServiceProvider;

class ParasutServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes( [
            __DIR__ . '/../config/parasut.php' => config_path( 'parasut.php' ),
        ], 'parasut-config' );
    }

    public function provides()
    {
        return [ 'Parasut' ];
    }

}
