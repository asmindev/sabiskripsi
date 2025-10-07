<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {

        // dd($request->user(), $request->user()?->role, $role);
        if (!in_array($request->user()->role, $role)) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
