<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized access.');
        }

        if (! $user->is_active) {
            abort(403, 'Your account is inactive.');
        }

        if (! $user->hasAnyRole([
            'super_admin',
            'admin',
        ])) {
            abort(403, 'You do not have administrator privileges.');
        }

        return $next($request);
    }
}