<?php

$router->get('user', 'UserController@getUser');
$router->post('user', 'UserController@createUser');
$router->delete('user', 'UserController@deleteUser');
$router->post('contact-message', 'MessagingController@contactMessage');

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
