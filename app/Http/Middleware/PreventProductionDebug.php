<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventProductionDebug
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') && config('app.debug')) {
            abort(503, 'Application debug mode must be disabled in production.');
        }

        return $next($request);
    }
}
