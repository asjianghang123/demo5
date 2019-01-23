<?php

namespace App\Providers;

use App\Http\Controllers\QueryAnalysis\SubnetWorkService;
use Illuminate\Support\ServiceProvider;

class SubnetWorkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('SubnetWork', function(){
            return new SubnetWorkService();
        });
    }
}
