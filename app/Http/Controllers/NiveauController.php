<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Niveau;
use App\Models\Logs;

class NiveauController extends Controller
{
    // Lister tous les niveaux avec pagination
    public function index()
    {
        $niveaux = Niveau::paginate(10);
        return response()->json($niveaux);
    }

    // Afficher un niveau spécifique
    public function show($id)
    {
        $niveau = Niveau::find($id);
        if (!$niveau) {
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }
        return response()->json($niveau);
    }

    // Créer un niveau (admin seulement)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }

        $request->validate([
            'nom' => 'required|string|unique:niveaux,nom',
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        $niveau = Niveau::create($request->all());

        Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => 'Création niveau',
                'details' => 'Niveau: ' . $niveau->nom,
                'ip' => $request->ip(),
            ]);


        return response()->json([
            'message' => 'Niveau créé avec succès',
            'niveau' => $niveau
        ], 201);
    }

    // Modifier un niveau existant (admin seulement)
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }

        $niveau = Niveau::find($id);
        if (!$niveau) {
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }

        $request->validate([
            'nom' => 'required|string|unique:niveaux,nom,' . $niveau->id,
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        $niveau->update($request->all());

         Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => 'Modification niveau',
                'details' => 'Niveau: '. $niveau->nom,
                'ip' => $request->ip(),
            ]);



        return response()->json([
            'message' => "Niveau mis à jour avec succès",
            "niveau" => $niveau
        ]);
    }

    // Supprimer un niveau (admin seulement)
    public function destroy($id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.'
            ], 403);
        }
        
        $niveau = Niveau::find($id);
        if (!$niveau) {
            return response()->json(['message' => "Niveau non trouvé"], 404);
        }
         Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => "Suppression niveau",
                "details" => "Nivau: ". $niveau->nom,
                "ip" =>$request->ip(),
            ]);
        
         // 🔹 Log
         Logs::create([
             "utilisateur_id" =>$user ?$user->id : null,
             "action" =>'Suppression niveau',
             "details" =>'Nivau: '.  $nomEcole,
             "ip" =>$request->ip(),
         ]);



       return response()->json(["message"=>"Nivau supprimé avec succès"]);
   }
}
