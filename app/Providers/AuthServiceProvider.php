<?php

namespace App\Providers;

use App\Models\UserModel;
use Illuminate\Support\ServiceProvider;
use Gate;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        // user fetching not needed here, the package tymon/jwt-auth handles it
//        $this->app['auth']->viaRequest('api', function ($request) {
//            if ($request->input('api_token')) {
//                return UserModel::where('api_token', $request->input('api_token'))->first();
//            }
//        });

        Gate::define('get-user', function(UserModel $authUser, UserModel $targetUser) {
            return $authUser->id == $targetUser->id;
        });
        Gate::define('update-user', function(UserModel $authUser, UserModel $targetUser) {
            return $authUser->id == $targetUser->id;
        });
        Gate::define('delete-user', function(UserModel $authUser, UserModel $targetUser) {
            return $authUser->id == $targetUser->id;
        });
    }
}
