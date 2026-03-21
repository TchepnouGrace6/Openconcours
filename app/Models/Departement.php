<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'ecole_id',
    ];

    // Relation avec l'école
    public function ecole()
    {
        return $this->belongsTo(Ecole::class);
    }

    // Relation avec les filières (plus tard)
    public function filieres()
    {
        return $this->hasMany(Filiere::class);
    }
}
