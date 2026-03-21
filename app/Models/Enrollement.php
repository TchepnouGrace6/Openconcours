<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Utilisateur;
use App\Models\ConcoursSession;
use App\Models\Paiement;
use App\Models\CentreExamen;
use App\Models\Salle;

class Enrollement extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'concours_session_id',
        'paiement_id',
        'centre_examen_id',
        'centre_depot_id',
        'salle_id',
        'numero_recu',
        'numero_enrollement',
        'numero_table',
        'statut',
        'prenom',
        'nom',
        'date_naissance',
        'sexe',
        'telephone',
        'adresse',
        'lieu_residence',
        'photo',
        'nationalite',
        'numero_cni',
        'date_delivrance_cni',
        'region_origine',
        'nom_pere',
        'nom_mere',
        'telephone_pere',
        'telephone_mere',
        'nom_tuteur',
        'telephone_tuteur',
        'statut_matrimoniale',
        'niveau_etude',
        'serie_bac',
        'mention_bac',
        'annee_diplome',
        'langue_parlee',
        'est_handicape',
        'type_handicap',
        'documents',
    ];

    /**
     * 🔹 Cast documents as array
     */
    protected $casts = [
        'documents' => 'array',
        'est_handicape' => 'boolean',
    ];

    /**
     * Relations
     */

    // Relation avec l'utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    // Relation avec la session du concours
    public function session()
    {
        return $this->belongsTo(ConcoursSession::class, 'concours_session_id');
    }

    // Relation avec le paiement
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // Relation avec le centre d'examen
    public function centreExamen()
    {
        return $this->belongsTo(CentreExamen::class, 'centre_examen_id');
    }

    // Relation avec la salle
    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }
}
