<?php

namespace SadnessDeployer\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\RouteCollection;
use League\Route\Strategy\ParamStrategy;
use SadnessDeployer\Http\Controllers\DeployController;

class RoutingServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        RouteCollection::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(RouteCollection::class, function () {
            $strategy = new ParamStrategy();
            $strategy->setContainer($this->container);

            $routes = new RouteCollection($this->container);
            $routes->setStrategy($strategy);

            // Register routes
            $routes->get('/', DeployController::class.'::index');
            $routes->get('/{task}', DeployController::class.'::index');
            $routes->get('/run/{hash}/{command}', DeployController::class.'::run');

            return $routes;
        });
    }
}
