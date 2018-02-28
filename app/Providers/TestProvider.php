<?php

namespace App\Providers;

use App\Services\OtherService;
use App\Services\TestService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class TestProvider extends ServiceProvider
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
        $this->app->bind(TestService::class,function ($app){
            return new TestService();
        });
        $this->app->bind('ErrorGen','App\Services\ErrorGenerator');
        $this->app->bind(UserService::class,function ($app){
            return new UserService();
        });
        $this->app->bind(OtherService::class,function ($app){
            return new OtherService();
        });
    }
}
