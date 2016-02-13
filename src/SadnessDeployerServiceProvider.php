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
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->group(['middleware' => WhitelistMiddleware::class], function() use ($router) {
            $router->get('deploy', DeployController::class.'@index');
        });
    }
}
