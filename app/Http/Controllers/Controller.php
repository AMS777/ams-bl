<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Tobscure\JsonApi\SerializerInterface;
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

            $jsonApiDocument = new JsonApi1_0Document;
            $jsonApiDocument->setErrorsFromKeyValueFormat($exception->errors());
            $exception->response = response()->json($jsonApiDocument, $exception->status);

            throw $exception;
        }
    }

    protected function getJsonApiResponse(
        $data, SerializerInterface $serializer, $httpStatus = 200
    ): JsonResponse
    {
        $jsonApiResource = new Resource($data, $serializer);
        $jsonApiDocument = new JsonApi1_0Document($jsonApiResource);

        return response()->json($jsonApiDocument, $httpStatus);
    }
}
