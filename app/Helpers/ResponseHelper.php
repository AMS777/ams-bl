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
    public static function getJsonApiResponse(Model $model, int $httpStatus = 200): JsonResponse
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
    public static function getJsonApiErrorResponse(array $errors, int $httpStatus): JsonResponse
    {
        $jsonApiDocument = new JsonApi1_0Document;
        $jsonApiDocument->setErrorsFromKeyValueFormat($errors);

        return response()->json($jsonApiDocument, $httpStatus);
    }

    public static function getNoContentJsonResponse(): JsonResponse
    {
        return response()->json(null, HttpStatusCodes::SUCCESS_NO_CONTENT);
    }

}
