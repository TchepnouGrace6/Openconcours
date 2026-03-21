<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationEnrollement;
use App\Models\CentreExamen;
use App\Models\ConcoursSession;
use App\Models\Enrollement;
use App\Models\Logs;
use App\Models\Paiement;
use App\Models\Salle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EnrollementController extends Controller
{
    /**
     * 📌 ADMIN : lister tous les enrôlements
     */
    public function index()
    {
        return response()->json(
            Enrollement::with([
                'utilisateur',
                'session.concours',
                'paiement',
                'centreExamen',
                'centreDepot',
                'salle',
            ])->orderByDesc('created_at')->paginate(10)
        );
    }

    /**
     * 📌 CANDIDAT / ADMIN : afficher un enrôlement
     */
    public function show($id)
    {
        $enrollement = Enrollement::with([
            'utilisateur',
            'session.concours',
            'paiement',
            'centreExamen',
            'centreDepot',
            'salle',
        ])->find($id);

        if (! $enrollement) {
            return response()->json(['message' => 'Enrôlement non trouvé'], 404);
        }

        return response()->json($enrollement);
    }

    /**
     * 📌 CANDIDAT : créer un enrôlement (NUMERO DE RECU SAISI)
     */
    public function store(Request $request)
    {
        $request->validate([
            'concours_session_id' => 'required|exists:concours_sessions,id',
            'centre_examen_id' => 'required|exists:centres_examen,id',
            'centre_depot_id' => 'required|exists:centre_depot,id',
            'numero_recu' => 'required|string',

            'prenom' => 'required|string',
            'nom' => 'required|string',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:masculin,feminin',
            'telephone' => 'required|string',
            'adresse' => 'required|string',
            'lieu_residence' => 'required|string',
            'nationalite' => 'required|string',
            'numero_cni' => 'required|string',
            'date_delivrance_cni' => 'required|date',
            'region_origine' => 'required|string',
            'nom_pere' => 'required|string',
            'nom_mere' => 'required|string',
            'telephone_pere' => 'required|string',
            'telephone_mere' => 'required|string',
            'statut_matrimoniale' => 'required|string',
            'niveau_etude' => 'required|string',
            'est_handicape' => 'required|boolean',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        DB::beginTransaction();

        try {
            /**
             * 🔍 Vérification paiement
             */
            $paiement = Paiement::where('numero_recu', $request->numero_recu)
                ->where('utilisateur_id', auth()->id())
                ->where('concours_session_id', $request->concours_session_id)
                ->where('statut', 'paye')
                ->first();

            if (! $paiement) {
                return response()->json([
                    'message' => 'Numéro de reçu invalide ou paiement non confirmé',
                ], 403);
            }

            /**
             * 🔒 Reçu déjà utilisé ?
             */
            if (Enrollement::where('numero_recu', $request->numero_recu)->exists()) {
                return response()->json([
                    'message' => 'Ce numéro de reçu a déjà servi à un enrôlement',
                ], 403);
            }

            /**
             * 🏫 Vérification centre / concours
             */
            $session = ConcoursSession::with('concours.centres')
                ->findOrFail($request->concours_session_id);

            $centreExamen = CentreExamen::findOrFail($request->centre_examen_id);

            if (! $session->concours->centres->contains($centreExamen->id)) {
                return response()->json(['message' => 'Centre invalide'], 403);
            }

            /**
             * 🪑 Attribution salle
             */
            $salle = $centreExamen->salles()
                ->withCount('enrollements')
                ->get()
                ->first(fn ($s) => $s->enrollements_count < $s->capacite);

            if (! $salle) {
                return response()->json(['message' => 'Aucune salle disponible'], 403);
            }

            $numeroTable = $salle->enrollements()->count() + 1;

            /**
             * 📸 Upload photo
             */
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')
                    ->store('enrollements/photos', 'public');
            }

            /**
             * 📄 Upload documents
             */
            $documentsPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $doc) {
                    $documentsPaths[] = $doc
                        ->store('enrollements/documents', 'public');
                }
            }

            /**
             * 🔢 Numéro enrôlement
             */
            $numeroEnrollement = 'ENR-'.date('Y').'-'.strtoupper(uniqid());

            /**
             * ✅ Création enrôlement
             */
            $enrollement = Enrollement::create([
                'utilisateur_id' => auth()->id(),
                'concours_session_id' => $request->concours_session_id,
                'paiement_id' => $paiement->id,
                'centre_examen_id' => $centreExamen->id,
                'centre_depot_id' => $request->centre_depot_id,
                'salle_id' => $salle->id,

                'numero_recu' => $request->numero_recu,
                'numero_enrollement' => $numeroEnrollement,
                'numero_table' => $numeroTable,

                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'date_naissance' => $request->date_naissance,
                'sexe' => $request->sexe,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'lieu_residence' => $request->lieu_residence,
                'nationalite' => $request->nationalite,
                'numero_cni' => $request->numero_cni,
                'date_delivrance_cni' => $request->date_delivrance_cni,
                'region_origine' => $request->region_origine,
                'nom_pere' => $request->nom_pere,
                'nom_mere' => $request->nom_mere,
                'telephone_pere' => $request->telephone_pere,
                'telephone_mere' => $request->telephone_mere,
                'statut_matrimoniale' => $request->statut_matrimoniale,
                'niveau_etude' => $request->niveau_etude,
                'est_handicape' => $request->est_handicape,

                'photo' => $photoPath,
                'documents' => json_encode($documentsPaths),

                'statut' => 'en_attente',
            ]);

            Logs::create([
                'utilisateur_id' => auth()->id(),
                'action' => 'Création enrôlement',
                'details' => "Enrôlement {$numeroEnrollement} | Reçu {$request->numero_recu}",
                'ip' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Enrôlement effectué avec succès',
                'enrollement' => $enrollement,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de l’enrôlement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 📌 ADMIN : valider / refuser un enrôlement
     */
    public function update(Request $request, $id)
    {
        $enrollement = Enrollement::find($id);
        if (! $enrollement) {
            return response()->json(['message' => 'Enrôlement non trouvé'], 404);
        }

        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé. Admin uniquement.',
            ], 403);
        }

        // Validation du statut
        $request->validate([
            'statut' => 'required|in:en_attente,valide,refuse',
        ]);

        // Met à jour le statut
        $enrollement->update(['statut' => $request->statut]);

        // Log de l'action
        Logs::create([
            'utilisateur_id' => auth()->id(),
            'action' => 'Mise à jour enrôlement',
            'details' => 'Enrôlement '.$enrollement->numero_enrollement.' => '.$request->statut,
            'ip' => $request->ip(),
        ]);

        // Si l'enrôlement est validé, envoie un mail au candidat
        if ($request->statut === 'valide') {
            Mail::to($enrollement->utilisateur->email)
                ->send(new ConfirmationEnrollement($enrollement));
        }

        return response()->json([
            'message' => 'Enrôlement mis à jour avec succès',
            'enrollement' => $enrollement,
        ]);
    }

    /**
     * 📌 ADMIN : supprimer
     */
    public function destroy($id)
    {
        Enrollement::findOrFail($id)->delete();

        return response()->json(['message' => 'Enrôlement supprimé']);
    }

    /**
     * 📌 CANDIDAT : PDF
     */
    public function exportPdf($id)
    {
        $enrollement = Enrollement::with(['utilisateur', 'session.concours', 'centreExamen', 'salle'])
            ->findOrFail($id);

        if ($enrollement->utilisateur_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return Pdf::loadView('pdf.enrollement', compact('enrollement'))
            ->download('enrollement_'.$enrollement->numero_enrollement.'.pdf');
    }

    // Retourne le nombre d'enrollements en attente de validation
    public function pendingCount()
    {
        $count = Enrollement::where('status', 'pending')->count(); // ou 'en_attente' selon ton champ

        return response()->json(['pending_count' => $count]);
    }
}
