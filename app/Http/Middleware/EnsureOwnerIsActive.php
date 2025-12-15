<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerIsActive
{
    /**
     * Handle an incoming request.
     *
     * Deactivated owners can login and navigate (GET requests),
     * but cannot perform actions (POST, PUT, PATCH, DELETE).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only check for owners (not admins or cleaners)
        if (!$user || !in_array($user->role, ['owner', 'authed-user'])) {
            return $next($request);
        }

        // Allow deactivated owners to view (GET requests)
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Block deactivated owners from performing actions
        if ($user->isDeactivated()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uw account is gedeactiveerd. U kunt geen wijzigingen maken. Neem contact op met de beheerder.'
                ], 403);
            }

            return back()->with('error', 'Uw account is gedeactiveerd. U kunt geen wijzigingen maken. Neem contact op met de beheerder.');
        }

        return $next($request);
    }
}
