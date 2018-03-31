<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Helpers\HttpStatusCodes;
use App\Helpers\ResponseHelper;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            // the commented response statement below is the original response
            // of Lumen. It shows the general misunderstanding and mixed used
            // of "authentication" and "authorization", this class and service
            // is about authentication but the response is about authorization.
            // The official status code is "401 Unauthorized", yet it refers to
            // authentication.
//            return response('Unauthorized.', 401);
            $errors = ['authentication' => ['The user is not authenticated.']];

            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_UNAUTHORIZED);
        }

        return $next($request);
    }
}
