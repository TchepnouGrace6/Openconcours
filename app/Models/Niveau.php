<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'filiere_id',
    ];

    // Relation avec la filière
    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    // Relation avec les concours (plus tard)
    public function concours()
    {
        return $this->hasMany(Concours::class);
    }
}
