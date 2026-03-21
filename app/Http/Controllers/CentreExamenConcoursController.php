<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CentreExamenConcours;
use App\Models\Logs;

class CentreExamenConcoursController extends Controller
{
    /**
     * Lister tous les centres associés aux concours
     */
    public function index()
    {
        $centres = CentreExamenConcours::with(['centreExamen', 'concours'])->paginate(10);
        return response()->json($centres);
    }

    /**
     * Afficher un centre spécifique pour un concours
     */
    public function show($id)
    {
        $centre = CentreExamenConcours::with(['centreExamen', 'concours'])->find($id);
        if (!$centre) return response()->json(['message' => 'Centre de concours non trouvé'], 404);
        return response()->json($centre);
    }

    /**
     * Créer une association centre-concours
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }

        $request->validate([
            'centre_examen_id' => 'required|exists:centres_examen,id',
            'concours_id' => 'required|exists:concours,id',
        ]);

        $centre = CentreExamenConcours::create($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création centre_examen_concours',
            'details' => 'Centre ID: ' . $request->centre_examen_id . ' associé au Concours ID: ' . $request->concours_id,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Association créée', 'centre' => $centre], 201);
    }

    /**
     * Mettre à jour une association
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }

        $centre = CentreExamenConcours::find($id);
        if (!$centre) return response()->json(['message' => 'Centre de concours non trouvé'], 404);

        $request->validate([
            'centre_examen_id' => 'required|exists:centres_examen,id',
            'concours_id' => 'required|exists:concours,id',
        ]);

        $centre->update($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Mise à jour centre_examen_concours',
            'details' => 'CentreExamenConcours ID: ' . $id,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Association mise à jour', 'centre' => $centre]);
    }

    /**
     * Supprimer une association
     */
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }
        
        $centre = CentreExamenConcours::find($id);
        if (!$centre) return response()->json(['message' => 'Centre de concours non trouvé'], 404);

        $centre->delete();

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression centre_examen_concours',
            'details' => 'CentreExamenConcours ID: ' . $id,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Association supprimée']);
    }
}
