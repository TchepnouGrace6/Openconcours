<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreExamen extends Model
{
    use HasFactory;

    protected $table = 'centres_examen';

    protected $fillable = [
        'nom',
        'adresse',
        'capacite', // nombre total de candidats pouvant être accueillis
    ];

    /**
     * Relation avec les salles
     */
    public function salles()
    {
        return $this->hasMany(Salle::class);
    }

    /**
     * Relation avec les enrôlements
     */
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class);
    }

    public function concours()
    {
        return $this->belongsToMany(Concours::class, 'concours_id');
    }
}
