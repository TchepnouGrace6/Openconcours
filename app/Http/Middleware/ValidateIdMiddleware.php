<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Récupérer tous les paramètres de route qui se terminent par 'id'
        $routeParameters = $request->route()->parameters();

        foreach ($routeParameters as $key => $value) {
            if (str_ends_with($key, 'id')) {
                // Vérifier que l'ID est un entier positif
                if (! is_numeric($value) || $value <= 0 || $value != (int) $value) {
                    return response()->json([
                        'message' => 'ID invalide',
                        'error' => 'INVALID_ID',
                        'parameter' => $key,
                    ], 400);
                }

                // Vérifier que l'ID n'est pas trop grand (prévention overflow)
                if ($value > 2147483647) { // Max INT en MySQL
                    return response()->json([
                        'message' => 'ID trop grand',
                        'error' => 'ID_TOO_LARGE',
                        'parameter' => $key,
                    ], 400);
                }
            }
        }

        return $next($request);
    }
}
