<?php

namespace App\Http\Middleware;

use Closure;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return abort(403);
        }

        if (! $user->isAdmin()) {
            return abort(403);
        }

        return $next($request);
    }
}
