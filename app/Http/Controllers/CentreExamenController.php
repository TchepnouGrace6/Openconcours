<?php

namespace App\Http\Controllers;

use App\Models\CentreExamen;
use App\Models\Logs;
use Illuminate\Http\Request;

class CentreExamenController extends Controller
{
    /**
     * 📌 Lister tous les centres d'examen
     */
    public function index()
    {
        $centres = CentreExamen::with('salles')->orderBy('nom')->get();

        return response()->json($centres);
    }

    /**
     * 📌 Afficher un centre d'examen spécifique
     */
    public function show($id)
    {
        $centre = CentreExamen::with('salles')->find($id);

        if (! $centre) {
            return response()->json(['message' => 'Centre d’examen non trouvé'], 404);
        }

        return response()->json($centre);
    }

    /**
     * 📌 Créer un centre d'examen (admin uniquement)
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé. Admin uniquement.'], 403);
        }

        $request->validate([
            'nom' => 'required|string|unique:centres_examen,nom',
            'adresse' => 'nullable|string',
            'capacite' => 'required|integer|min:1',
        ]);

        $centre = CentreExamen::create($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création centre d’examen',
            'details' => 'Centre: '.$centre->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Centre d’examen créé avec succès',
            'centre' => $centre,
        ], 201);
    }

    /**
     * 📌 Modifier un centre d'examen (admin uniquement)
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé. Admin uniquement.'], 403);
        }

        $centre = CentreExamen::find($id);
        if (! $centre) {
            return response()->json(['message' => 'Centre d’examen non trouvé'], 404);
        }

        $request->validate([
            'nom' => 'required|string|unique:centres_examen,nom,'.$centre->id,
            'adresse' => 'nullable|string',
            'capacite' => 'required|integer|min:1',
        ]);

        $centre->update($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Modification centre d’examen',
            'details' => 'Centre: '.$centre->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Centre d’examen mis à jour avec succès',
            'centre' => $centre,
        ]);
    }

    /**
     * 📌 Supprimer un centre d'examen (admin uniquement)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé. Admin uniquement.'], 403);
        }

        $centre = CentreExamen::find($id);
        if (! $centre) {
            return response()->json(['message' => 'Centre d’examen non trouvé'], 404);
        }

        $nom = $centre->nom;
        $centre->delete();

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression centre d’examen',
            'details' => 'Centre: '.$nom,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Centre d’examen supprimé avec succès']);
    }
}
