<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function getUser()
    {
        return [
            'email' => 'test@test.test',
            'name' => 'Test Name',
        ];
    }
}
