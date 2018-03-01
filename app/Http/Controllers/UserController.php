<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\HttpStatusCodes;

class UserController extends Controller
{
    public function createUser(Request $request): Response
    {
        $user = UserModel::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ]);

        return (new Response($user, HttpStatusCodes::SUCCESS_CREATED));
    }

    public function getUser(Request $request): Response
    {
        if ( ! $request->input('email')) {

            return (new Response(null, HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST));
        }

        return (new Response(
            UserModel::where('email', $request->input('email'))->first(),
            HttpStatusCodes::SUCCESS_OK
        ));
    }

    public function deleteUser(Request $request): Response
    {
        UserModel::where('email', $request->input('email'))->delete();

        return (new Response(null, HttpStatusCodes::SUCCESS_NO_CONTENT));
    }
}
