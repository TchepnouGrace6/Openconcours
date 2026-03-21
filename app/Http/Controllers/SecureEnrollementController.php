<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationEnrollement;
use App\Models\CentreExamen;
use App\Models\ConcoursSession;
use App\Models\Enrollement;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SecureEnrollementController extends SecureBaseController
{
    /**
     * 📌 ADMIN : lister tous les enrôlements
     */
    public function index(Request $request)
    {
        // Validation des paramètres de pagination
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'status' => 'string|in:en_attente,valide,refuse',
            'search' => 'string|max:255',
        ]);

        $query = Enrollement::with([
            'utilisateur:id,nom,email',
            'session.concours:id,nom',
            'paiement:id,numero_recu,montant',
            'centreExamen:id,nom',
            'centreDepot:id,nom',
            'salle:id,nom',
        ]);

        // Filtres
        if ($request->has('status')) {
            $query->where('statut', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('numero_enrollement', 'like', "%{$search}%");
            });
        }

        $enrollements = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        $this->logUserAction('Consultation liste enrôlements',
            "Page {$request->get('page', 1)}, Filtres: ".json_encode($request->only(['status', 'search'])));

        return $this->successResponse($enrollements);
    }

    /**
     * 📌 CANDIDAT / ADMIN : afficher un enrôlement
     */
    public function show($id, Request $request)
    {
        $validatedId = $this->validateId($id);
        if ($validatedId instanceof \Illuminate\Http\JsonResponse) {
            return $validatedId;
        }

        $enrollement = Enrollement::with([
            'utilisateur:id,nom,email',
            'session.concours',
            'paiement',
            'centreExamen',
            'centreDepot',
            'salle',
        ])->find($validatedId);

        if (! $enrollement) {
            return $this->errorResponse('Enrôlement non trouvé', 404);
        }

        // Vérifier les permissions d'accès
        if (! $this->checkResourceAccess($enrollement)) {
            return $this->errorResponse('Accès non autorisé à cette ressource', 403);
        }

        $this->logUserAction('Consultation enrôlement', "ID: {$enrollement->id}");

        return $this->successResponse($enrollement);
    }

    /**
     * 📌 CANDIDAT : créer un enrôlement
     */
    public function store(Request $request)
    {
        try {
            // Validation stricte
            $validatedData = $request->validate([
                'concours_session_id' => 'required|integer|exists:concours_sessions,id',
                'centre_examen_id' => 'required|integer|exists:centres_examen,id',
                'centre_depot_id' => 'required|integer|exists:centre_depot,id',
                'numero_recu' => 'required|string|max:50',

                'prenom' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
                'nom' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
                'date_naissance' => 'required|date|before:today|after:1900-01-01',
                'sexe' => 'required|in:masculin,feminin',
                'telephone' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20',
                'adresse' => 'required|string|max:255',
                'lieu_residence' => 'required|string|max:100',
                'nationalite' => 'required|string|max:50',
                'numero_cni' => 'required|string|max:50|unique:enrollements,numero_cni',
                'date_delivrance_cni' => 'required|date|before:today|after:1900-01-01',
                'region_origine' => 'required|string|max:100',
                'nom_pere' => 'required|string|max:100',
                'nom_mere' => 'required|string|max:100',
                'telephone_pere' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20',
                'telephone_mere' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20',
                'statut_matrimoniale' => 'required|string|in:celibataire,marie,divorce,veuf',
                'niveau_etude' => 'required|string|max:100',
                'est_handicape' => 'required|boolean',

                'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'documents' => 'nullable|array|max:5',
                'documents.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ]);

            // Nettoyer les données
            $validatedData = $this->sanitizeInput($validatedData);

        } catch (ValidationException $e) {
            return $this->errorResponse('Données invalides', 422, $e->errors());
        }

        DB::beginTransaction();

        try {
            // Vérification du paiement
            $paiement = Paiement::where('numero_recu', $validatedData['numero_recu'])
                ->where('utilisateur_id', auth()->id())
                ->where('concours_session_id', $validatedData['concours_session_id'])
                ->where('statut', 'paye')
                ->first();

            if (! $paiement) {
                return $this->errorResponse('Numéro de reçu invalide ou paiement non confirmé', 403);
            }

            // Vérifier si le reçu n'a pas déjà été utilisé
            if (Enrollement::where('numero_recu', $validatedData['numero_recu'])->exists()) {
                return $this->errorResponse('Ce numéro de reçu a déjà été utilisé', 403);
            }

            // Vérifier la validité du centre pour ce concours
            $session = ConcoursSession::with('concours.centres')
                ->findOrFail($validatedData['concours_session_id']);

            $centreExamen = CentreExamen::findOrFail($validatedData['centre_examen_id']);

            if (! $session->concours->centres->contains($centreExamen->id)) {
                return $this->errorResponse('Centre d\'examen non valide pour ce concours', 403);
            }

            // Attribution d'une salle disponible
            $salle = $centreExamen->salles()
                ->withCount('enrollements')
                ->get()
                ->first(fn ($s) => $s->enrollements_count < $s->capacite);

            if (! $salle) {
                return $this->errorResponse('Aucune salle disponible dans ce centre', 403);
            }

            $numeroTable = $salle->enrollements()->count() + 1;

            // Upload sécurisé de la photo
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = 'photo_'.auth()->id().'_'.time().'.'.$photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('enrollements/photos', $filename, 'public');
            }

            // Upload sécurisé des documents
            $documentsPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $index => $doc) {
                    $filename = 'doc_'.auth()->id().'_'.time().'_'.$index.'.'.$doc->getClientOriginalExtension();
                    $documentsPaths[] = $doc->storeAs('enrollements/documents', $filename, 'public');
                }
            }

            // Génération du numéro d'enrôlement unique
            $numeroEnrollement = 'ENR-'.date('Y').'-'.strtoupper(uniqid());

            // Création de l'enrôlement
            $enrollement = Enrollement::create(array_merge($validatedData, [
                'utilisateur_id' => auth()->id(),
                'paiement_id' => $paiement->id,
                'salle_id' => $salle->id,
                'numero_enrollement' => $numeroEnrollement,
                'numero_table' => $numeroTable,
                'photo' => $photoPath,
                'documents' => json_encode($documentsPaths),
                'statut' => 'en_attente',
            ]));

            $this->logUserAction('Création enrôlement',
                "Enrôlement {$numeroEnrollement} | Reçu {$validatedData['numero_recu']}");

            DB::commit();

            return $this->successResponse($enrollement, 'Enrôlement effectué avec succès', 201);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->logUserAction('Erreur création enrôlement',
                "Erreur: {$e->getMessage()}");

            return $this->errorResponse('Erreur lors de l\'enrôlement', 500);
        }
    }

    /**
     * 📌 ADMIN : valider / refuser un enrôlement
     */
    public function update(Request $request, $id)
    {
        $validatedId = $this->validateId($id);
        if ($validatedId instanceof \Illuminate\Http\JsonResponse) {
            return $validatedId;
        }

        $enrollement = Enrollement::find($validatedId);
        if (! $enrollement) {
            return $this->errorResponse('Enrôlement non trouvé', 404);
        }

        try {
            $validatedData = $request->validate([
                'statut' => 'required|in:en_attente,valide,refuse',
                'commentaire' => 'nullable|string|max:500',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Données invalides', 422, $e->errors());
        }

        $ancienStatut = $enrollement->statut;
        $enrollement->update($validatedData);

        $this->logUserAction('Mise à jour enrôlement',
            "Enrôlement {$enrollement->numero_enrollement}: {$ancienStatut} => {$validatedData['statut']}");

        // Envoi d'email si validé
        if ($validatedData['statut'] === 'valide') {
            try {
                Mail::to($enrollement->utilisateur->email)
                    ->send(new ConfirmationEnrollement($enrollement));
            } catch (\Exception $e) {
                // Log l'erreur mais ne fait pas échouer la requête
                $this->logUserAction('Erreur envoi email',
                    "Enrôlement {$enrollement->numero_enrollement}: {$e->getMessage()}");
            }
        }

        return $this->successResponse($enrollement, 'Enrôlement mis à jour avec succès');
    }

    /**
     * 📌 CANDIDAT : exporter en PDF
     */
    public function exportPdf($id, Request $request)
    {
        $validatedId = $this->validateId($id);
        if ($validatedId instanceof \Illuminate\Http\JsonResponse) {
            return $validatedId;
        }

        $enrollement = Enrollement::with([
            'utilisateur',
            'session.concours',
            'centreExamen',
            'salle',
        ])->find($validatedId);

        if (! $enrollement) {
            return $this->errorResponse('Enrôlement non trouvé', 404);
        }

        // Vérifier les permissions
        if (! $this->checkResourceAccess($enrollement)) {
            return $this->errorResponse('Accès non autorisé', 403);
        }

        $this->logUserAction('Export PDF enrôlement', "ID: {$enrollement->id}");

        try {
            return Pdf::loadView('pdf.enrollement', compact('enrollement'))
                ->download('enrollement_'.$enrollement->numero_enrollement.'.pdf');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la génération du PDF', 500);
        }
    }
}
