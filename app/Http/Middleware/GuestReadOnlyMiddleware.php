<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GuestReadOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'guest') {
            // Guest hanya boleh GET/HEAD (lihat data)
            if (!in_array($request->method(), ['GET', 'HEAD'])) {
                abort(403, 'Guest hanya diperbolehkan melihat data.');
            }
        }

        return $next($request);
    }
}
