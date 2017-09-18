<?php
namespace Santanu\Lumen_Oauth;

use Laravel\Lumen\Application;

use Santanu\Lumen_Oauth\Interfaces\Oauthable;
use Santanu\Lumen_Oauth\Storage\MySQL;

use OAuth2\Request;
use OAuth2\Server;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;

class Router
{
    private $app, $model;

    public function __construct(Oauthable $model, Application $app)
    {
		$this->app = $app;
        $this->model = $model;
    }

	public function accessToken() {
		$storage = new MySQL($this->model);
		$server = new Server($storage, ['allow_implicit' => true]);
        $request = Request::createFromGlobals();
		
		$server->addGrantType(new UserCredentials($storage));
		$token = $server->handleTokenRequest($request);

		return response()->json(json_decode($token->getResponseBody()), $token->getStatusCode());
	}
	
	public function refreshToken() {
		$storage = new MySQL($this->model);
		$server = new Server($storage, ['allow_implicit' => true]);
        $request = Request::createFromGlobals();

		$server->addGrantType(new RefreshToken($storage, ['always_issue_new_refresh_token' => true]));
		$token = $server->handleTokenRequest($request);

		return response()->json(json_decode($token->getResponseBody()), $token->getStatusCode());
	}
}