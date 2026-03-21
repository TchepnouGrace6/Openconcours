<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class SecureBaseController extends Controller
{
    /**
     * Valider et nettoyer un ID
     */
    protected function validateId($id, $fieldName = 'id')
    {
        if (! is_numeric($id) || $id <= 0) {
            return response()->json([
                'message' => "ID invalide pour {$fieldName}",
                'error' => 'INVALID_ID',
            ], 400);
        }

        return (int) $id;
    }

    /**
     * Vérifier les permissions d'accès à une ressource
     */
    protected function checkResourceAccess($resource, $userId = null)
    {
        $user = auth()->user();

        // Admin a accès à tout
        if ($user->isAdmin()) {
            return true;
        }

        // Candidat ne peut accéder qu'à ses propres ressources
        if ($userId && $resource->utilisateur_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Logger une action utilisateur
     */
    protected function logUserAction($action, $details = '', ?Request $request = null)
    {
        if (! $request) {
            $request = request();
        }

        try {
            Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => $action,
                'details' => $details,
                'ip' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du logging', [
                'error' => $e->getMessage(),
                'action' => $action,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Réponse standardisée pour les erreurs
     */
    protected function errorResponse($message, $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => $code,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Réponse standardisée pour les succès
     */
    protected function successResponse($data = null, $message = 'Opération réussie', $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Nettoyer les données d'entrée
     */
    protected function sanitizeInput(array $data)
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return trim(strip_tags($value));
            }

            return $value;
        }, $data);
    }
}
