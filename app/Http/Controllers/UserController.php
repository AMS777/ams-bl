<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use Log;

class UserController extends Controller
{
    public function createUser(Request $request): JsonResponse
    {
        $charactersRangeSizeForPassword = env('PASSWORD_MIN_CHARACTERS') . ','
            . env('PASSWORD_MAX_CHARACTERS');

        $this->validate_ExceptionResponseJsonApi($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|between:' . $charactersRangeSizeForPassword,
        ], [
            'unique' => 'The :attribute ":input" is already used.',
        ]);

        $user = UserModel::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => $request->input('password'),
        ]);

        return $this->getJsonApiResponse($user, HttpStatusCodes::SUCCESS_CREATED);
    }

    public function getUser(Request $request): JsonResponse
    {
        $this->validate_ExceptionResponseJsonApi($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ], [
            'exists' => 'The :attribute ":input" does not exist.',
        ]);

        $user = UserModel::where('email', $request->input('email'))
            ->where('password', $request->input('password'))
            ->first();

        return $this->getJsonApiResponse($user);
    }

    public function deleteUser(Request $request): JsonResponse
    {
        UserModel::where('email', $request->input('email'))->delete();

        return $this->getNoContentJsonResponse();
    }
}
