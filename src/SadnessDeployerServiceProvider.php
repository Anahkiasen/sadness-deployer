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
        $this->publishes([
            __DIR__.'/../config/deploy.php' => config_path('deploy.php'),
        ]);

        $this->app->bind(Deployer::class, function ($app) {
            $deployer = new Deployer(new CommandsRunner());
            $deployer->setConfiguration($app['config']->get('deploy'));

            return $deployer;
        });
    }

    public function boot()
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->group(['middleware' => WhitelistMiddleware::class], function () use ($router) {
            $router->get('deploy', DeployController::class.'@index');
            $router->get('deploy/setup', DeployController::class.'@setup');
        });
    }
}
