<?php

namespace SadnessDeployer\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Plates\Engine;
use SadnessDeployer\Configuration;

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
     */
    public function register()
    {
        $this->container->share(Engine::class, function () {
            $configuration = $this->container->get(Configuration::class);
            $folder = $configuration->get('paths.views');

           return new Engine($folder);
        });
    }
}
