<?php

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('users', 'UserController@getUser');
    $router->post('users', 'UserController@createUser');
    $router->delete('users', 'UserController@deleteUser');
    $router->post('contact-message', 'MessagingController@contactMessage');
});

//$router->group(['prefix' => 'view-email-on-browser'], function () use ($router) {
//
//    $router->get('/contact-message', function () {
//
//        return new App\Mail\ContactMessageEmail([
//            'name' => 'Test Name',
//            'email' => 'test@test.test',
//            'message' => 'Test message.',
//        ]);
//    });
//});
