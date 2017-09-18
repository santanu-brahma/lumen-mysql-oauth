<?php
namespace Santanu\Lumen_Oauth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Santanu\Lumen_Oauth\Models\Client;

class ClientAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $client_id = $request->getUser();
        $client_secret = $request->getPassword();
        $client = Client::findByClientId($client_id);
        if (!$client || $client->getSecret() != $client_secret) {
			$error = array(
				'error' => 'invalid_token',
				'error_description' => 'Client API credential is not valid.'
			);
			return response()->json($error, 401);
        } else {
            return $next($request);
        }
    }
}
