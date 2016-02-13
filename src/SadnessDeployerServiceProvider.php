<?php

namespace SadnessDeployer;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use SadnessDeployer\Http\DeployController;
use SadnessDeployer\Http\WhitelistMiddleware;

class SadnessDeployerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'sadness-deployer');
    }

    public function boot()
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->group(['middleware' => WhitelistMiddleware::class], function () use ($router) {
            $router->get('deploy', DeployController::class.'@index');
        });
    }
}
