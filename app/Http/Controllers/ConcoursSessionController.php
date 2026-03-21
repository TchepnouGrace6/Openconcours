<?php

namespace App\Http\Controllers;

use App\Models\ConcoursSession;
use App\Models\Logs;
use Illuminate\Http\Request;

class ConcoursSessionController extends Controller
{
    // Lister toutes les sessions (candidats et admins)
    public function index()
    {
        $sessions = ConcoursSession::with('concours')->paginate(10);

        return response()->json($sessions);
    }

    // Afficher une session spécifique
    public function show($id)
    {
        $session = ConcoursSession::with('concours')->find($id);
        if (! $session) {
            return response()->json(['message' => 'Session non trouvée'], 404);
        }

        return response()->json($session);
    }

    // Créer une session (admin seulement)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $request->validate([
            'concours_id' => 'required|exists:concours,id',
            'nom_session' => 'required|string',
            'date_session' => 'required|date',
            // 'centre_examen' => 'required|string',
            'salle' => 'nullable|string',
            'centres_examen_id' => 'required|exists:centres_examen,id',
        ]);

        $session = ConcoursSession::create($request->all());

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création session concours',
            'details' => 'Session: '.$session->nom_session,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Session créée avec succès',
            'session' => $session,
        ], 201);
    }

    // Modifier une session (admin seulement)
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $session = ConcoursSession::find($id);
        if (! $session) {
            return response()->json(['message' => 'Session non trouvée'], 404);
        }

        $session->update($request->all());

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Modification session concours',
            'details' => 'Session: '.$session->nom_session,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Session mise à jour avec succès',
            'session' => $session,
        ]);
    }

    // Supprimer une session (admin seulement)
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $session = ConcoursSession::find($id);
        if (! $session) {
            return response()->json(['message' => 'Session non trouvée'], 404);
        }

        $nomSession = $session->nom_session;
        $session->delete();

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression session concours',
            'details' => 'Session: '.$nomSession,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Session supprimée avec succès']);
    }
}
