<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Logs;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    // Lister les départements (paginé, accessible aux candidats)
    public function index()
    {
        $departements = Departement::with('ecole')->paginate(10);

        return response()->json($departements);
    }

    // Afficher un département spécifique
    public function show($id)
    {
        $departement = Departement::with('ecole')->find($id);
        if (! $departement) {
            return response()->json(['message' => 'Département non trouvé'], 404);
        }

        return response()->json($departement);
    }

    // Créer un département (admin seulement)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $request->validate([
            'nom' => 'required|string|unique:departements,nom',
            'description' => 'nullable|string',
            'ecole_id' => 'required|exists:ecoles,id',
        ]);

        $departement = Departement::create($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création département',
            'details' => 'Département: '.$departement->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Département créé avec succès',
            'departement' => $departement,
        ], 201);
    }

    // Modifier un département (admin seulement)
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $departement = Departement::find($id);
        if (! $departement) {
            return response()->json(['message' => 'Département non trouvé'], 404);
        }

        $request->validate([
            'nom' => 'required|string|unique:departements,nom,'.$departement->id,
            'description' => 'nullable|string',
            'ecole_id' => 'required|exists:ecoles,id',
        ]);

        $departement->update($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Modification département',
            'details' => 'Département: '.$departement->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Département mis à jour avec succès',
            'departement' => $departement,
        ]);
    }

    // Supprimer un département (admin seulement)
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $departement = Departement::find($id);
        if (! $departement) {
            return response()->json(['message' => 'Département non trouvé'], 404);
        }
        $nomDepartement = $departement->nom;
        $departement->delete();

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression département',
            'details' => 'Département: '.$nomDepartement,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Département supprimé avec succès']);
    }
}
