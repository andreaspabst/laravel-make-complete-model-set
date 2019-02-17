<?php

namespace AndreasPabst\LaravelMakeCompleteModelSet;

use AndreasPabst\LaravelMakeCompleteModelSet\Console\Commands\MakeCompleteModelSet;
use Illuminate\Support\ServiceProvider;

class LaravelMakeCompleteModelSetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            MakeCompleteModelSet::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
