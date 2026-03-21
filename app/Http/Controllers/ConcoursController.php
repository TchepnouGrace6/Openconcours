<?php

namespace App\Http\Controllers;

use App\Models\Concours;
use App\Models\Logs;
use Illuminate\Http\Request;

class ConcoursController extends Controller
{
    // Lister les concours (candidats et admins)
    public function index()
    {
        $concours = Concours::with(['filiere', 'niveau'])->paginate(10);

        return response()->json($concours);
    }

    // Afficher un concours spécifique
    public function show($id)
    {
        $concours = Concours::with(['filiere', 'niveau'])->find($id);
        if (! $concours) {
            return response()->json(['message' => 'Concours non trouvé'], 404);
        }

        return response()->json($concours);
    }

    // Créer un concours (admin seulement)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'filiere_id' => 'required|exists:filieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'date_concours' => 'nullable|date',
            'date_limite_dossier' => 'nullable|date',
            'date_limite_paiement' => 'nullable|date',
            'taux_reussite' => 'nullable|integer',
            'taux_echec' => 'nullable|integer',
        ]);

        $concours = Concours::create($request->all());

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création concours',
            'details' => 'Concours: '.$concours->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Concours créé avec succès',
            'concours' => $concours,
        ], 201);
    }

    // Modifier un concours (admin seulement)
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $concours = Concours::find($id);
        if (! $concours) {
            return response()->json(['message' => 'Concours non trouvé'], 404);
        }

        $concours->update($request->all());

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Modification concours',
            'details' => 'Concours: '.$concours->nom,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Concours mis à jour avec succès',
            'concours' => $concours,
        ]);
    }

    // Supprimer un concours (admin seulement)
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $concours = Concours::find($id);
        if (! $concours) {
            return response()->json(['message' => 'Concours non trouvé'], 404);
        }

        $nomConcours = $concours->nom;
        $concours->delete();

        // 🔹 Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression concours',
            'details' => 'Concours: '.$nomConcours,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Concours supprimé avec succès']);
    }
}
