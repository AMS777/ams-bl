<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use App\Helpers\ResponseHelper;
use Tymon\JWTAuth\JWTAuth;
use Gate;

class UserController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

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
            'remember_token' => str_random(100),
        ]);

        return ResponseHelper::getJsonApiResponse($user, HttpStatusCodes::SUCCESS_CREATED);
    }

    public function getToken(Request $request): JsonResponse
    {
        // the OAuth 2.0 specification used by ember-simple-auth requires
        // the user identification field to be named "username":
        // https://tools.ietf.org/html/rfc6749#section-4.3.2
        // set "email" parameter on request for the errors to be descriptive
        // and not misleading
        $request->merge(['email' => $request->input('username')]);
        $this->validate_ExceptionResponseJsonApi($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ], [
            'exists' => 'The :attribute ":input" does not exist.',
        ]);

        if (( ! $token = $this->jwt->attempt($request->only('email', 'password')))
            || ( ! $this->jwt->user())
        ) {
            // errors follow the OAuth 2.0 specification:
            // https://tools.ietf.org/html/rfc6749#section-5.1
            $oauth2TokenError = [
                'error' => 'invalid_client',
                'error_title' => 'Authentication Error',
                'error_description' => 'There is no account with those email and password.',
            ];

            return ResponseHelper::oauth2TokenResponse_Error($oauth2TokenError);
        }

        return ResponseHelper::oauth2TokenResponse_Success($this->jwt->user(), $token);
    }

    public function getUser(Request $request, int $userId): JsonResponse
    {
        $user = UserModel::find($userId);

        if (( ! $user) || Gate::denies('get-user', $user)) {
            $errors = ['authorization' => ['User account cannot be read. Try to login again.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_FORBIDDEN);
        }

        return ResponseHelper::getJsonApiResponse($user);
    }

    public function updateUser(Request $request, int $userId): JsonResponse
    {
        $user = UserModel::find($userId);

        if (( ! $user) || Gate::denies('update-user', $user)) {
            $errors = ['authorization' => ['User account cannot be read. Try to login again.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_FORBIDDEN);
        }

        $charactersRangeSizeForPassword = env('PASSWORD_MIN_CHARACTERS') . ','
            . env('PASSWORD_MAX_CHARACTERS');

        $this->validate_ExceptionResponseJsonApi($request, [
            'data.id' => 'required|exists:users,id',
            'data.attributes.email' => 'email|unique:users,email',
            'data.attributes.password' => 'between:' . $charactersRangeSizeForPassword,
        ], [
            'unique' => 'The :attribute ":input" is already used.',
        ]);

        if ($request->filled('data.attributes.name')) {
            $user->name = $request->input('data.attributes.name');
        }
        if ($request->filled('data.attributes.email')) {
            $user->email = $request->input('data.attributes.email');
        }
        if ($request->filled('data.attributes.password')) {
            $user->password = $this->getPasswordHash($request->input('data.attributes.password'));
        }

        $user->save();

        return ResponseHelper::getNoContentJsonResponse();
    }

    public function deleteUser(int $userId): JsonResponse
    {
        $user = UserModel::find($userId);

        if (( ! $user) || Gate::denies('delete-user', $user)) {
            $errors = ['authorization' => ['User account cannot be deleted.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_FORBIDDEN);
        }

        UserModel::destroy($userId);

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
