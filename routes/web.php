<?php

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('users/{userId}', ['middleware' => 'auth', 'uses' => 'UserController@getUser']);
    $router->post('users', 'UserController@createUser');
    $router->patch('users/{userId}', ['middleware' => 'auth', 'uses' => 'UserController@updateUser']);
    $router->delete('users/{userId}', ['middleware' => 'auth', 'uses' => 'UserController@deleteUser']);
    $router->post('get-token', 'UserController@getToken');
    $router->post('request-reset-password', 'UserController@requestResetPassword');
    $router->post('reset-password', 'UserController@resetPassword');
    $router->post('contact-message', 'MessagingController@contactMessage');
    $router->post('verify-email', 'UserController@verifyEmail');

    $router->group(['prefix' => 'view-email-on-browser'], function () use ($router) {
        $router->get('/contact-message', function () {
            return new App\Mail\ContactMessage([
                'name' => 'Test Name',
                'email' => 'test@test.test',
                'message' => 'Test message.',
            ]);
        });
        $router->get('/register-confirmation', function () {
            $user = new App\Models\UserModel();
            $user->name = 'Test Name';
            $user->verify_email_token = 'hY5zg8567VQyXg3FNd5AgjXomiT2Di0PQ8kfLDZ91Vvsg35EVDg8RfaL9hub7DPGv2DrfvcIG9fYimbSWmSwMIMGfVFP9xRcqo8b';
            return new App\Mail\RegisterConfirmation($user);
        });
        $router->get('/request-reset-password', function () {
            $user = new App\Models\UserModel();
            $user->name = 'Test Name';
            $user->reset_password_token = 'hY5zg8567VQyXg3FNd5AgjXomiT2Di0PQ8kfLDZ91Vvsg35EVDg8RfaL9hub7DPGv2DrfvcIG9fYimbSWmSwMIMGfVFP9xRcqo8b';
            return new App\Mail\RequestResetPassword($user);
        });
        $router->get('/delete-account-confirmation', function () {
            $user = new App\Models\UserModel();
            $user->name = 'Test Name';
            return new App\Mail\DeleteAccountConfirmation($user);
        });
    });

});