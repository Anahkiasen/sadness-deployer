<?php

namespace SadnessDeployer\Http\Middlewares;

use Illuminate\Auth\Access\AuthorizationException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Relay\MiddlewareInterface;

class WhitelistMiddleware implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    protected $allowed = [];

    /**
     * WhitelistMiddleware constructor.
     *
     * @param \string[] $allowed
     */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * Middleware logic to be invoked.
     *
     * @param RequestInterface                  $request  The request.
     * @param Response                          $response The response.
     * @param callable|MiddlewareInterface|null $next     The next middleware.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function __invoke(RequestInterface $request, Response $response, callable $next = null)
    {
        //$ip = $request->ip();
        $ip = '127.0.0.1';
        if (!$this->isAllowedIp($ip)) {
            throw new AuthorizationException('Invalid IP '.$ip);
        }

        return $next($request, $response);
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    protected function isAllowedIp($ip)
    {
        // Get allowed IPs
        $isAllowed = in_array($ip, $this->allowed, true);

        return $isAllowed;
    }
}
