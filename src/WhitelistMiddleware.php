<?php
namespace SadnessDeployer;

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
        $allowed = env('DEPLOY_IPS');
        $allowed = (array) explode(',', $allowed);

        if (!in_array($request->ip(), $allowed)) {
            throw new AuthorizationException;
        }

        return $next($request);
    }
}
