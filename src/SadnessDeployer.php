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
use SadnessDeployer\Providers\PlatesServiceProvider;
use SadnessDeployer\Providers\RequestServiceProvider;
use SadnessDeployer\Providers\RoutingServiceProvider;
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
        $this->container = $this->makeContainer();

        // Create configuration
        $this->configuration = new Configuration(array_merge([
            'base_path'   => realpath(__DIR__.'/..'),
            'allowed_ips' => [
                '127.0.0.1',
            ],
            'scm'         => [
                'url'    => 'git@github.com:foo/bar.git',
                'branch' => 'master',
            ],
        ], $configuration));

        // Load dotenv file
        try {
            $dotenv = new Dotenv(__DIR__.'/..');
            $dotenv->load();
        } catch (InvalidPathException $exception) {
            // ..
        }
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

        $container->share(Configuration::class, function() {
            return $this->configuration;
        });

        return $container;
    }

    /**
     * Run the application
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
