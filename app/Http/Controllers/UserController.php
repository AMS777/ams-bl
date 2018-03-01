<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

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
        User::create([
            'email' => $request->email,
            'name' => $request->name,
        ]);
    }

    public function deleteUser(Request $request)
    {
        User::where('email', $request->email)->delete();
    }
}
