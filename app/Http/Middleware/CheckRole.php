<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            // Not authenticated, handle as appropriate (e.g., redirect to login or abort)
            // For API-like behavior or strictness, aborting is fine.
            // For web, you might redirect: return redirect('login');
            abort(401, 'Unauthenticated.');
        }

        $user = Auth::user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Forbidden - You do not have the required role.');
        }

        return $next($request);
    }
}
