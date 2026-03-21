<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    /**
     * 📌 CANDIDAT : créer un paiement (auto-généré)
     */
    public function store(Request $request)
    {
        $request->validate([
            'concours_session_id' => 'required|exists:concours_sessions,id',
            'montant' => 'required|numeric|min:0',
            'moyen_paiement' => 'required|string|in:mobile money,carte bancaire,espèces', // tu peux ajouter d'autres moyens
        ]);

        // Génération automatique du numéro de reçu et référence transaction
        $numeroRecu = 'REC-'.date('Y').'-'.str_pad(Paiement::count() + 1, 6, '0', STR_PAD_LEFT);
        $referenceTransaction = strtoupper(Str::random(10));

        // Création du paiement
        $paiement = Paiement::create([
            'utilisateur_id' => auth()->id(),
            'concours_session_id' => $request->concours_session_id,
            'montant' => $request->montant,
            'moyen_paiement' => $request->moyen_paiement,
            'numero_recu' => $numeroRecu,
            'reference_transaction' => $referenceTransaction,
            'statut' => 'paye', // directement payé, plus besoin de validation admin
        ]);

        // Log
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Création paiement',
            'details' => 'Paiement créé - Reçu : '.$numeroRecu.' | Référence : '.$referenceTransaction,
            'ip' => $request->ip(),
        ]);

        // Retour simplifié pour le candidat
        return response()->json([
            'concours_session_id' => $paiement->concours_session_id,
            'montant' => $paiement->montant,
            'moyen_paiement' => $paiement->moyen_paiement,
            'numero_recu' => $paiement->numero_recu,
            'reference_transaction' => $paiement->reference_transaction,
            'statut' => $paiement->statut,
        ], 201);
    }

    /**
     * 📌 CANDIDAT : voir ses paiements
     */
    public function mesPaiements()
    {
        $paiements = Paiement::where('utilisateur_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($paiements);
    }

    /**
     * 📌 ADMIN : lister tous les paiements (lecture seule)
     */
    public function index()
    {
        $paiements = Paiement::with(['utilisateur', 'session.concours'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($paiements);
    }
}
