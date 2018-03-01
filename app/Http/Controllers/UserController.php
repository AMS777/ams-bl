<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        User::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ]);
    }

    public function getUser(Request $request): ?User
    {
        return User::where('email', $request->input('email'))->first();
    }

    public function deleteUser(Request $request)
    {
        User::where('email', $request->input('email'))->delete();
    }
}
