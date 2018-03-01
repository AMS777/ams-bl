<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\HttpStatusCodes;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        UserModel::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ]);
    }

    public function getUser(Request $request): ?UserModel
    {
        return UserModel::where('email', $request->input('email'))->first();
    }

    public function deleteUser(Request $request)
    {
        UserModel::where('email', $request->input('email'))->delete();
    }
}
