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
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

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
            $routes->get($this->url.'/', DeployController::class.'::index');
            $routes->get($this->url.'/{task}', DeployController::class.'::index');
            $routes->get($this->url.'/run/{task}/{command}', DeployController::class.'::run');

            return $routes;
        });
    }
}
