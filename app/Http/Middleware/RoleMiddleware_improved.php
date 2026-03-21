<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifier l'authentification
        if (!auth()->check()) {
            Log::warning('Tentative d\'accès non authentifié', [
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'message' => 'Non authentifié',
                'error' => 'UNAUTHENTICATED'
            ], 401);
        }

        $user = auth()->user();
        
        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!in_array($user->role, $roles)) {
            Log::warning('Tentative d\'accès non autorisé', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_roles' => $roles,
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'message' => 'Accès interdit. Rôle insuffisant.',
                'error' => 'FORBIDDEN',
                'required_roles' => $roles,
                'user_role' => $user->role
            ], 403);
        }

        // Log des accès autorisés pour audit
        Log::info('Accès autorisé', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'route' => $request->route()->getName(),
            'method' => $request->method()
        ]);

        return $next($request);
    }
}