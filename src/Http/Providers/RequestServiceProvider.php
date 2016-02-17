<?php

namespace SadnessDeployer\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class RequestServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        ServerRequestInterface::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(ServerRequestInterface::class, function () {
            $request = ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            );

            // Mock internal router
            $uri = new Uri($request->getUri()->getQuery() ?: '/');
            $request = $request->withUri($uri);

            return $request;
        });
    }
}
