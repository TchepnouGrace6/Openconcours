<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        if (auth()->user()->role !== $role) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        return $next($request);
    }
}
