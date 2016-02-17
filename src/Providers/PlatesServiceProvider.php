<?php
namespace SadnessDeployer\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Plates\Engine;

class PlatesServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [Engine::class];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->share(Engine::class, function () {
           return new Engine(__DIR__.'/../../views');
        });
    }
}
