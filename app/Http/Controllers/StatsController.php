<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Ecole;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getStats()
    {
        try {
            $stats = [
                'ecoles' => Ecole::count(),
                'candidats' => Candidat::count(),
                'satisfaction' => 98, // Vous pouvez calculer cela depuis une table de satisfaction
                'concours' => DB::table('concours')->where('statut', 'ouvert')->count(),
            ];

            return response()->json($stats, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des statistiques',
            ], 500);
        }
    }

    public function getConcours()
    {
        try {
            $concours = DB::table('concours')
                ->where('statut', 'ouvert')
                ->select('id', 'nom', 'ecole', 'date_limite', 'places_disponibles', 'frais', 'centres')
                ->orderBy('date_limite', 'asc')
                ->limit(3)
                ->get();

            return response()->json($concours, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des concours',
            ], 500);
        }
    }
}
