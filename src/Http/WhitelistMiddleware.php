<?php

namespace SadnessDeployer\Http;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class WhitelistMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @throws AuthorizationException
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isAllowedIp($request)) {
            throw new AuthorizationException('Invalid IP '.$request->ip());
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isAllowedIp(Request $request)
    {
        // Get allowed IPs
        $allowed = config('deploy.allowed_ips');
        $isAllowed = in_array($request->ip(), $allowed, true);

        return $isAllowed;
    }
}
