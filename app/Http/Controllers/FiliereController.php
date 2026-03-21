<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filiere;
use App\Models\Logs;

class FiliereController extends Controller
{
    // Lister toutes les filières (candidat/admin)
    public function index()
    {
        $filieres = Filiere::with('departement')->paginate(10);
        return response()->json($filieres);
    }

    // Afficher une filière spécifique
    public function show($id)
    {
        $filiere = Filiere::with('departement')->find($id);
        if (!$filiere) return response()->json(['message' => 'Filière non trouvée'], 404);
        return response()->json($filiere);
    }

    // Créer une filière (admin seulement)
    public function store(Request $request)
    {

        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }

        $request->validate([
            'nom' => 'required|string|unique:filieres,nom',
            'description' => 'nullable|string',
            'departement_id' => 'required|exists:departements,id',
        ]);

        $filiere = Filiere::create($request->all());

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création filière',
            'details' => 'Filière: ' . $filiere->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Filière créée avec succès',
            'filiere' => $filiere
        ], 201);
    }

    // Modifier une filière (admin seulement)
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }

        $filiere = Filiere::find($id);
        if (!$filiere) return response()->json(['message' => 'Filière non trouvée'], 404);

        $request->validate([
            'nom' => 'required|string|unique:filieres,nom,' . $filiere->id,
            'departement_id' => 'required|exists:departements,id',
        ]);

        $filiere->update($request->all());

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Modification filière',
            'details' => 'Filière: ' . $filiere->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Filière mise à jour avec succès',
            'filiere' => $filiere
        ]);
    }

    // Supprimer une filière (admin seulement)
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }
        
        $filiere = Filiere::find($id);
        if (!$filiere) return response()->json(['message' => 'Filière non trouvée'], 404);

        $nomFiliere = $filiere->nom;
        $filiere->delete();

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression filière',
            'details' => 'Filière: ' . $nomFiliere,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Filière supprimée avec succès']);
    }
}