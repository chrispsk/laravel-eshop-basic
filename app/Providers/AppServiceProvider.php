<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # View::share('Name', 'Chris');
        View::composer('*', function($view){
            $view->with('userData', Auth::user());
        });
    }
}
