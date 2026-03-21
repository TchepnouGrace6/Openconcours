<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ecole;
use App\Models\Logs;

class EcoleController extends Controller
{
    // Lister toutes les écoles avec pagination
    public function index()
    {
        $ecoles = Ecole::paginate(10);
        return response()->json($ecoles);
    }

    // Afficher une école spécifique
    public function show($id)
    {
        $ecole = Ecole::find($id);
        if (!$ecole) {
            return response()->json(['message' => 'École non trouvée'], 404);
        }
        return response()->json($ecole);
    }

    // Créer une école (admin seulement)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }
        $request->validate([
            'nom' => 'required|string|unique:ecoles,nom',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string',
            'pays' => 'nullable|string',
        ]);

        $ecole = Ecole::create($request->all());

        Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => 'Création école',
                'details' => 'École: ' . $ecole->nom,
                'ip' => $request->ip(),
            ]);


        return response()->json([
            'message' => 'École créée avec succès',
            'ecole' => $ecole
        ], 201);
    }

    // Modifier une école existante (admin seulement)
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }
        
        $ecole = Ecole::find($id);
        if (!$ecole) {
            return response()->json(['message' => 'École non trouvée'], 404);
        }

        $request->validate([
            'nom' => 'required|string|unique:ecoles,nom,' . $ecole->id,
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string',
            'pays' => 'nullable|string',
        ]);

        $ecole->update($request->all());

        Logs::create([
        'utilisateur_id' => auth()->id(),
        'action' => 'Modification école',
        'details' => 'École: ' . $ecole->nom,
        'ip' => $request->ip(),
    ]);

        return response()->json([
            'message' => 'École mise à jour avec succès',
            'ecole' => $ecole
        ]);
    }

    // Supprimer une école (admin seulement)
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }
        $ecole = Ecole::find($id);
        if (!$ecole) {
            return response()->json(['message' => 'École non trouvée'], 404);
        }
        $nomEcole = $ecole->nom; // garder le nom avant suppression
        $ecole->delete();

         // 🔹 Log
            Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => 'Suppression école',
                'details' => 'École: ' . $nomEcole,
                'ip' => request()->ip(),
            ]);

        return response()->json(['message' => 'École supprimée avec succès']);
    }
}
