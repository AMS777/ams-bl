<?php

$router->get('user', 'UserController@getUser');
$router->post('user', 'UserController@createUser');
$router->delete('user', 'UserController@deleteUser');
