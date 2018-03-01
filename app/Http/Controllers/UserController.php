<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Log;

class UserController extends Controller
{
    public function getUser()
    {
        return [
            'email' => 'test@test.test',
            'name' => 'Test Name',
        ];
    }

    public function createUser(Request $request)
    {
        $user = new User;

        $user->email = $request->email;
        $user->name = $request->name;

        $user->save();
    }
}
