<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Departement;
use App\Models\Concours;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'departement_id',
    ];

    // Relation avec le département
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    // Relation avec les concours (plus tard)
    public function concours()
    {
        return $this->hasMany(Concours::class);
    }
}
