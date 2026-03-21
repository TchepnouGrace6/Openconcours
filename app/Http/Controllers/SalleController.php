<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    /**
     * Lister toutes les salles
     */
    public function index()
    {
        $salles = Salle::with('centre')->paginate(10);

        return response()->json($salles);
    }

    /**
     * Afficher une salle spécifique
     */
    public function show($id)
    {
        $salle = Salle::with('centre')->find($id);
        if (! $salle) {
            return response()->json(['message' => 'Salle non trouvée'], 404);
        }

        return response()->json($salle);
    }

    /**
     * Créer une salle
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $request->validate([
            'nom_salle' => 'required|string',
            'capacite' => 'required|integer|min:1',
            'centre_examen_id' => 'required|exists:centres_examen,id',
        ]);

        $salle = Salle::create($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création salle',
            'details' => 'Salle: '.$salle->nom_salle,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Salle créée', 'salle' => $salle], 201);
    }

    /**
     * Mettre à jour une salle
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $salle = Salle::find($id);
        if (! $salle) {
            return response()->json(['message' => 'Salle non trouvée'], 404);
        }

        $request->validate([
            'nom_salle' => 'required|string',
            'capacite' => 'required|integer|min:1',
            'centre_examen_id' => 'required|exists:centres_examen,id',
        ]);

        $salle->update($request->all());

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Mise à jour salle',
            'details' => 'Salle ID: '.$id,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Salle mise à jour', 'salle' => $salle]);
    }

    /**
     * Supprimer une salle
     */
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        $salle = Salle::find($id);
        if (! $salle) {
            return response()->json(['message' => 'Salle non trouvée'], 404);
        }

        $salle->delete();

        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Suppression salle',
            'details' => 'Salle ID: '.$id,
            'ip' => request()->ip(),
        ]);

        return response()->json(['message' => 'Salle supprimée']);
    }
}
