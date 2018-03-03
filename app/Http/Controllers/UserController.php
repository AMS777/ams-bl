<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use App\JsonApi\JsonApiSerializer_User;

class UserController extends Controller
{
    public function createUser(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required',
        ], [
            'unique' => 'The :attribute ":input" is already used.',
        ]);

        $user = UserModel::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ]);

        return $this->getJsonApiResponse(
            $user, new JsonApiSerializer_User, HttpStatusCodes::SUCCESS_CREATED
        );
    }

    public function getUser(Request $request): JsonResponse
    {
        $this->validate_ExceptionResponseJsonApi($request, [
            'email' => 'required|email|exists:users',
        ], [
            'exists' => 'The :attribute ":input" does not exist.',
        ]);

        $user = UserModel::where('email', $request->input('email'))->first();

        return $this->getJsonApiResponse($user, new JsonApiSerializer_User);
    }

    public function deleteUser(Request $request): Response
    {
        UserModel::where('email', $request->input('email'))->delete();

        return (new Response(null, HttpStatusCodes::SUCCESS_NO_CONTENT));
    }
}
