<?php
namespace Santanu\Lumen_Oauth\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider as LumenServiceProvider;

use Santanu\Lumen_Oauth\Middleware\Authenticate;
use Santanu\Lumen_Oauth\Middleware\ClientAuthenticate;
use Santanu\Lumen_Oauth\Router;
use Santanu\Lumen_Oauth\Storage\MySQL;

use OAuth2\Request;
use OAuth2\Server;

class ServiceProvider extends LumenServiceProvider
{
    private $configs = [
        'oauth', 'auth'
    ];

    public function boot() {
        foreach ($this->configs as $config) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $config . '.php' => $this->globalConfig($config),
            ]);
        }
    }

    public function register() {
        $this->app->configure('auth');
        $this->app->make('config')->set('auth', require __DIR__ . '/../config/auth.php');
        foreach ($this->configs as $config) {
            $this->mergeConfigFrom($this->globalConfig($config), $config);
        }

        $config = $this->app['config'];
        $userModel = new $config['oauth.userModel'];

        $this->app->routeMiddleware([
            'oauth' => Authenticate::class,
            'oauth.basic.client' => ClientAuthenticate::class,
        ]);

        $this->app['auth']->viaRequest('oauth', function () use ($userModel) {
			$server = new Server(new MySQL($userModel), ['allow_implicit' => true]);
            $request = Request::createFromGlobals();
            if ($server->verifyResourceRequest($request)) {
				$response = $server->getAccessTokenData($request);
				return $userModel::find($response)->first();
            }
        });

        $this->app->singleton('oauth.routes', function ($app) use ($userModel) {
            return new Router($userModel, $app, Response::create());
        });
    }

    public function globalConfig($config) {
        return app()->basePath() . '/config/' . $config . '.php';
    }

    public function localConfig($config) {
        $local = $this->app['config'];
        return $local->get($config, []);
    }
}