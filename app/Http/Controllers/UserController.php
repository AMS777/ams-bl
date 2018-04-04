<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use App\Helpers\ResponseHelper;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Gate;
use App\Helpers\MailHelper;
use App\Mail\RequestResetPassword;

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
            'password' => Hash::make($request->input('data.attributes.password')),
        ]);

        if ( ! $user) {
            $errors = ['create' => ['Error creating user account.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        }

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
            'data.attributes.email' => 'nullable|email|unique:users,email,' . $userId,
            'data.attributes.password' => 'nullable|between:' . $charactersRangeSizeForPassword,
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
            $user->password = Hash::make($request->input('data.attributes.password'));
        }

        if ( ! $user->save()) {
            $errors = ['update' => ['Error updating user account.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        }

        return ResponseHelper::getNoContentJsonResponse();
    }

    public function deleteUser(int $userId): JsonResponse
    {
        $user = UserModel::find($userId);

        if (( ! $user) || Gate::denies('delete-user', $user)) {
            $errors = ['authorization' => ['User account cannot be deleted.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_FORBIDDEN);
        }

        if ( ! UserModel::destroy($userId)) {
            $errors = ['delete' => ['Error deleting user account.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        }

        return ResponseHelper::getNoContentJsonResponse();
    }

    public function requestResetPassword(Request $request): JsonResponse
    {
        $this->validate_ExceptionResponseJsonApi($request, [
            'data.attributes.email' => 'required|email|exists:users,email',
        ], [
            'exists' => 'The :attribute ":input" does not exist.',
        ]);

        $user = UserModel::where('email', $request->input('data.attributes.email'))->first();
        $user->reset_password_token = str_random(100);

        if ( ! $user->save()) {
            $errors = ['update' => ['Error updating user account.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        }

        $jsonApiResponse = MailHelper::sendEmail($user->email, new RequestResetPassword($user));

        return $jsonApiResponse;
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $charactersRangeSizeForPassword = env('PASSWORD_MIN_CHARACTERS') . ','
            . env('PASSWORD_MAX_CHARACTERS');

        $this->validate_ExceptionResponseJsonApi($request, [
            'data.attributes.reset_password_token' => 'required|exists:users,reset_password_token',
            'data.attributes.password' => 'required|between:' . $charactersRangeSizeForPassword,
        ], [
            'data.attributes.reset_password_token.exists' => 'The reset password token is invalid.',
        ]);

        $user = UserModel::where(
            'reset_password_token',
            $request->input('data.attributes.reset_password_token')
        )->first();

        $user->password = Hash::make($request->input('data.attributes.password'));
        $user->reset_password_token = null;

        if ( ! $user->save()) {
            $errors = ['update' => ['Error updating user account.']];
            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        }

        return ResponseHelper::getNoContentJsonResponse();
    }
}
