<?php

namespace SadnessDeployer;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use League\Container\Container;
use League\Container\ImmutableContainerAwareInterface;
use League\Container\ImmutableContainerAwareTrait;
use League\Container\ReflectionContainer;
use Psr\Http\Message\ServerRequestInterface;
use Relay\MiddlewareInterface;
use Relay\RelayBuilder;
use SadnessDeployer\Http\Middlewares\LeagueRouteMiddleware;
use SadnessDeployer\Http\Middlewares\WhitelistMiddleware;
use SadnessDeployer\Http\Providers\PlatesServiceProvider;
use SadnessDeployer\Http\Providers\RequestServiceProvider;
use SadnessDeployer\Http\Providers\RoutingServiceProvider;
use SadnessDeployer\Tasks\Deploy;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;

class SadnessDeployer implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        // Create configuration
        $this->configuration = $this->makeConfiguration($configuration);
        $this->container     = $this->makeContainer();

        // Load dotenv file
        try {
            $dotenv = new Dotenv(__DIR__.'/..');
            $dotenv->load();
        } catch (InvalidPathException $exception) {
            // ..
        }
    }

    /**
     * @param array $configuration
     *
     * @return Configuration
     */
    protected function makeConfiguration(array $configuration)
    {
        $defaults = [
            'paths'       => [
                'app'      => realpath(getcwd().'/..'),
                'cache'    => realpath(__DIR__.'/../cache'),
                'views'    => realpath(__DIR__.'/../views'),
                'deployer' => realpath(getcwd()),
            ],
            'allowed_ips' => [
                '127.0.0.1',
            ],
            'scm'         => [
                'url'    => 'git@github.com:foo/bar.git',
                'branch' => 'master',
            ],
            'tasks' => [
                Deploy::class,
            ]
        ];

        $configuration = array_replace_recursive($defaults, $configuration);
        $configuration = new Configuration($configuration);

        return $configuration;
    }

    /**
     * @return Container
     */
    protected function makeContainer()
    {
        $container = new Container();
        $container->delegate(new ReflectionContainer());

        $container->addServiceProvider(new RequestServiceProvider());
        $container->addServiceProvider(new RoutingServiceProvider());
        $container->addServiceProvider(new PlatesServiceProvider());

        $container->share(Configuration::class, function () {
            return $this->configuration;
        });

        return $container;
    }

    /**
     * Run the application.
     */
    public function run()
    {
        // Create Request and Response
        $request  = $this->container->get(ServerRequestInterface::class);
        $response = new Response();

        $builder = new RelayBuilder(function ($callable) {
            return is_string($callable) ? $this->container->get($callable) : $callable;
        });

        // Apply middlewares
        $relay    = $builder->newInstance($this->getMiddlewares());
        $response = $relay($request, $response);

        (new SapiEmitter())->emit($response);
    }

    /**
     * @return MiddlewareInterface[]
     */
    protected function getMiddlewares()
    {
        return [
            new WhitelistMiddleware($this->configuration->get('allowed_ips')),
            LeagueRouteMiddleware::class,
        ];
    }
}
