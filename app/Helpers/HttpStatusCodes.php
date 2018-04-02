<?php

namespace App\Helpers;

abstract class HttpStatusCodes {

    const SUCCESS_OK = 200;
    const SUCCESS_CREATED = 201;
    const SUCCESS_NO_CONTENT = 204;
    const CLIENT_ERROR_BAD_REQUEST = 400;
    const CLIENT_ERROR_UNAUTHORIZED = 401;
    const CLIENT_ERROR_FORBIDDEN = 403;
    const METHOD_NOT_ALLOWED = 405;
    const CLIENT_ERROR_CONFLICT = 409;
    const CLIENT_ERROR_UNPROCESSABLE_ENTITY = 422;
}
