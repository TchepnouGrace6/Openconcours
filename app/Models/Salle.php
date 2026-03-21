<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CentreExamen;
use App\Models\Enrollement;

class Salle extends Model
{
    use HasFactory;
 protected $table = 'salles';

    protected $fillable = [
        'centre_examen_id',
        'nom_salle',
        'capacite', // nombre de candidats que cette salle peut contenir
    ];

    /**
     * Relation avec le centre
     */
    public function centre()
    {
        return $this->belongsTo(CentreExamen::class, 'centre_examen_id');
    }

    /**
     * Relation avec les enrôlements
     */
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class);
    }
}
