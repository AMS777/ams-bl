<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\HttpStatusCodes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserModel;
use Tobscure\JsonApi\SerializerInterface;
use App\JsonApi\JsonApiSerializer_User;
use Tobscure\JsonApi\Resource;
use App\JsonApi\JsonApi1_0Document;

class Controller extends BaseController
{
    protected function validate_ExceptionResponseJsonApi(
        Request $request, array $rules, array $messages = []
    ): void
    {
        try {

            $this->validate($request, $rules, $messages);

        } catch (ValidationException $exception) {

            $exception->response = $this->getJsonApiErrorResponse($exception->errors(), $exception->status);

            throw $exception;
        }
    }

    protected function getJsonApiResponse(Model $model, int $httpStatus = 200): JsonResponse
    {
        $jsonApiDocument = null;

        $serializer = $this->getJsonApiSerializerFromModel($model);

        if ( ! empty($serializer)) {
            $jsonApiResource = new Resource($model, $serializer);
            $jsonApiDocument = new JsonApi1_0Document($jsonApiResource);
        }

        return response()->json($jsonApiDocument, $httpStatus);
    }

    private function getJsonApiSerializerFromModel(Model $model): ?SerializerInterface
    {
        $serializer = null;

        if ($model instanceof UserModel) {
            $serializer = new JsonApiSerializer_User;
        }

        return $serializer;
    }

    protected function getJsonApiErrorResponse(array $errors, int $httpStatus): JsonResponse
    {
        $jsonApiDocument = new JsonApi1_0Document;
        $jsonApiDocument->setErrorsFromKeyValueFormat($errors);

        return response()->json($jsonApiDocument, $httpStatus);
    }

    protected function getNoContentJsonResponse(): JsonResponse
    {
        return response()->json(null, HttpStatusCodes::SUCCESS_NO_CONTENT);
    }
}
