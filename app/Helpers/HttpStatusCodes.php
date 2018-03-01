<?php

namespace App\Helpers;

abstract class HttpStatusCodes {

    const SUCCESS_OK = 200;
    const SUCCESS_CREATED = 201;
    const SUCCESS_NO_CONTENT = 204;
    const CLIENT_ERROR_BAD_REQUEST = 400;
    const CLIENT_ERROR_UNPROCESSABLE_ENTITY = 422;
}
