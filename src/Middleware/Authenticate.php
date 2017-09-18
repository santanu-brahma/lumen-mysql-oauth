<?php
namespace Santanu\Lumen_Oauth\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
			$error = array(
				'error' => 'invalid_token',
				'error_description' => 'Oauth access token is not found or invalid.'
			);
			return response()->json($error, 401);
        }
        return $next($request);
    }
}
