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
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isAllowedIp($request)) {
            throw new AuthorizationException;
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
        $allowed   = env('DEPLOY_IPS');
        $allowed   = (array) explode(',', $allowed);
        $allowed[] = '127.0.0.1';

        $isAllowed = in_array($request->ip(), $allowed);

        return $isAllowed;
    }
}
