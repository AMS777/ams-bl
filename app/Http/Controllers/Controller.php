<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\JsonApi\JsonApi1_0Document;

class Controller extends BaseController
{
    public function validate_ExceptionResponseJsonApi(
        Request $request, array $rules, array $messages = []
    ) {
        try {

            $this->validate($request, $rules, $messages);

        } catch (ValidationException $exception) {

            $jsonApiDocument = new JsonApi1_0Document;
            $jsonApiDocument->setErrorsFromKeyValueFormat($exception->errors());
            $exception->response = response()->json($jsonApiDocument, $exception->status);

            throw $exception;
        }
    }
}
