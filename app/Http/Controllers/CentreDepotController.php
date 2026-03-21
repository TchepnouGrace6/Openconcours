<?php

namespace App\Http\Controllers;

use App\Models\CentreDepot;
use App\Models\Logs;
use Illuminate\Http\Request;

class CentreDepotController extends Controller
{
    /**
     * Lister tous les centres de dépôt
     */
    public function index()
    {
        $centres = CentreDepot::paginate(10);

        return response()->json($centres);
    }

    /**
     * Afficher un centre de dépôt
     */
    public function show($id)
    {
        $centre = CentreDepot::find($id);
        if (! $centre) {
            return response()->json(['message' => 'Centre de dépôt non trouvé'], 404);
        }

        return response()->json($centre);
    }

    /**
     * Créer un centre de dépôt
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $request->validate([
            'concours_id' => 'required|exists:concours,id',
            'nom' => 'required|string',
            'adresse' => 'required|string',
        ]);

        $centre = CentreDepot::create($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création centre_depot',
            'details' => 'Centre: '.$centre->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Centre de dépôt créé', 'centre' => $centre], 201);
    }

    /**
     * Mettre à jour un centre de dépôt
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $centre = CentreDepot::find($id);
        if (! $centre) {
            return response()->json(['message' => 'Centre de dépôt non trouvé'], 404);
        }

        $request->validate([
            'concours_id' => 'required|exists:concours,id',
            'nom' => 'required|string',
            'adresse' => 'required|string',
        ]);

        $centre->update($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Mise à jour centre_depot',
            'details' => 'CentreDepot ID: '.$id,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Centre de dépôt mis à jour', 'centre' => $centre]);
    }

    /**
     * Supprimer un centre de dépôt
     */
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $centre = CentreDepot::find($id);
        if (! $centre) {
            return response()->json(['message' => 'Centre de dépôt non trouvé'], 404);
        }

        $centre->delete();

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression centre_depot',
            'details' => 'CentreDepot ID: '.$id,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Centre de dépôt supprimé']);
    }
}
