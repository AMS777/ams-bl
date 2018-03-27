<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    public function createUser(Request $request): JsonResponse
    {
        $charactersRangeSizeForPassword = env('PASSWORD_MIN_CHARACTERS') . ','
            . env('PASSWORD_MAX_CHARACTERS');

        $this->validate_ExceptionResponseJsonApi($request, [
            'data.attributes.email' => 'required|email|unique:users,email',
            'data.attributes.name' => 'required',
            'data.attributes.password' => 'required|between:' . $charactersRangeSizeForPassword,
        ], [
            'unique' => 'The :attribute ":input" is already used.',
        ]);

        $user = UserModel::create([
            'email' => $request->input('data.attributes.email'),
            'name' => $request->input('data.attributes.name'),
            'password' => $this->getPasswordHash($request->input('data.attributes.password')),
        ]);

        return ResponseHelper::getJsonApiResponse($user, HttpStatusCodes::SUCCESS_CREATED);
    }

    public function getUser(Request $request): JsonResponse
    {
        $this->validate_ExceptionResponseJsonApi($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ], [
            'exists' => 'The :attribute ":input" does not exist.',
        ]);

        $user = UserModel::where('email', $request->input('email'))->first();

        if ( ! $this->verifyPassword($request->input('password'), $user['password'])) {

            $errors = ['mixed' => ['There is no account with those email and password.']];

            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY);
        }

        return ResponseHelper::getJsonApiResponse($user);
    }

    public function deleteUser(Request $request): JsonResponse
    {
        UserModel::where('email', $request->input('email'))->delete();

        return ResponseHelper::getNoContentJsonResponse();
    }

    private function getPasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
