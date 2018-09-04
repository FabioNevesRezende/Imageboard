<?php

namespace Ibbr\Http\Middleware;

use Closure;

class XFrameOptionsHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('X-Frame-Options', 'deny');

        return $response;
    }
}
