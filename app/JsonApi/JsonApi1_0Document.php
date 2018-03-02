<?php

namespace App\JsonApi;

use Tobscure\JsonApi\Document;

class JsonApi1_0Document extends Document
{
    protected $jsonapi = ['version' => '1.0'];
    protected $errors;

    public function setErrorsFromKeyValueFormat($errorsOnKeyValueFormat)
    {
        $errorsOnJsonApiFormat = [];

        foreach ($errorsOnKeyValueFormat as $errorParameter => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $errorsOnJsonApiFormat[] = [
                    'source' => [
                        'parameter' => $errorParameter,
                    ],
                    'title' => $errorMessage,
                ];
            }
        }

        $this->errors = $errorsOnJsonApiFormat;

        return $this;
    }
}
