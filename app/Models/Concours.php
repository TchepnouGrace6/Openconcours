<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Enrollement;
use App\Models\CentreExamen;

class Concours extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'filiere_id',
        'niveau_id',
        'date_concours',
        'date_limite_dossier',
        'date_limite_paiement',
        'taux_reussite',
        'taux_echec',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function enrollements()
    {
        return $this->hasMany(Enrollement::class); // plus tard pour gérer les candidats
    }
    public function centres()
    {
        return $this->belongsToMany(CentreExamen::class, 'centre_examen_concours');
    }

      public function centresdepot()
    {
        return $this->belongsToMany(CentreDepot::class, 'centre_depot');
    }
}
