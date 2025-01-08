<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBlockedUser
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_blocked) {
            return response()->json([
                'message' => 'Access denied. Your account is blocked.'
            ], 403); // Forbidden
        }

        return $next($request);
    }
}
