<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Helpers\ResponseHelper;

class Controller extends BaseController
{
    protected function validate_ExceptionResponseJsonApi(
        Request $request, array $rules, array $messages = []
    ): void
    {
        try {

            $this->validate($request, $rules, $messages);

        } catch (ValidationException $exception) {

            $exception->response = ResponseHelper::getJsonApiErrorResponse(
                $exception->errors(),
                $exception->status
            );

            throw $exception;
        }
    }
}
