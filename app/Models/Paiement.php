<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ConcoursSession;
use App\Models\Utilisateur;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'concours_session_id',
        'montant',
        'moyen_paiement',
        'numero_recu',
        'reference_transaction',
        'statut',
    ];

    // 🔗 Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function session()
    {
        return $this->belongsTo(ConcoursSession::class, 'concours_session_id');
    }
}
