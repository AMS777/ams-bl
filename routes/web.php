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
});

//$router->group(['prefix' => 'view-email-on-browser'], function () use ($router) {
//
//    $router->get('/contact-message', function () {
//
//        return new App\Mail\ContactMessage([
//            'name' => 'Test Name',
//            'email' => 'test@test.test',
//            'message' => 'Test message.',
//        ]);
//    });
//});
