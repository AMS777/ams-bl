<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserModel;
use Tobscure\JsonApi\SerializerInterface;
use App\JsonApi\JsonApiSerializer_User;
use Tobscure\JsonApi\Resource;
use App\JsonApi\JsonApi1_0Document;

class ResponseHelper
{
    public static function getJsonApiResponse(
        Model $model, int $httpStatus = HttpStatusCodes::SUCCESS_OK
    ): JsonResponse
    {
        $jsonApiDocument = null;

        $serializer = self::getJsonApiSerializerFromModel($model);

        if ( ! empty($serializer)) {
            $jsonApiResource = new Resource($model, $serializer);
            $jsonApiDocument = new JsonApi1_0Document($jsonApiResource);
        }

        return response()->json($jsonApiDocument, $httpStatus);
    }

    private static function getJsonApiSerializerFromModel(Model $model): ?SerializerInterface
    {
        $serializer = null;

        if ($model instanceof UserModel) {
            $serializer = new JsonApiSerializer_User;
        }

        return $serializer;
    }

    /*
     * Errors on Lumen's validate() format:
     * $errors = [
     *   'email' => [
     *     0 => 'The email must be a valid email address.',
     *     1 => 'The email "not.existing.email@test.test" does not exist.',
     *   ],
     *   'password' => [
     *     0 => 'The password field is required.',
     *   ],
     * ];
     */
    public static function getJsonApiErrorResponse(
        array $errors, int $httpStatus = HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST
    ): JsonResponse
    {
        $jsonApiDocument = new JsonApi1_0Document;
        $jsonApiDocument->setErrorsFromKeyValueFormat($errors);

        return response()->json($jsonApiDocument, $httpStatus);
    }

    public static function getNoContentJsonResponse(): JsonResponse
    {
        return response()->json(null, HttpStatusCodes::SUCCESS_NO_CONTENT);
    }

    public static function oauth2TokenResponse_Success(string $accessToken): JsonResponse
    {
        // OAuth 2.0 access token response used by ember-simple-auth:
        // https://tools.ietf.org/html/rfc6749#section-4.3.3
        $oauth2TokenData = [
            'access_token' => $accessToken,
        ];

        return response()->json($oauth2TokenData, HttpStatusCodes::SUCCESS_OK);
    }

    public static function oauth2TokenResponse_Error(array $oauth2TokenError): JsonResponse
    {
        return response()->json($oauth2TokenError, HttpStatusCodes::CLIENT_ERROR_UNAUTHORIZED);
    }
}
