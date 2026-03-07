<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowIframe
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->remove('X-Frame-Options');
        return $response;
    }
}
