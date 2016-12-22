<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Auth;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('unique_friend', function ($attribute, $value, $parameters, $validator) {
            $user_id = Auth::id();
            return !(DB::table('friend_user')->where('friend_id', "$value")->where('user_id', "$user_id")->exists());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
