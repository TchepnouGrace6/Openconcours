<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcoursSession extends Model
{
    use HasFactory;

    protected $table = 'concours_sessions';

    protected $fillable = [
        'concours_id',
        'nom_session',
        'date_session',
        'centres_examen_id',

    ];

    // Relation avec le concours
    public function concours()
    {
        return $this->belongsTo(Concours::class);
    }

    public function salles()
    {
        return $this->belongsTo(Salle::class);
    }

    public function centres()
    {
        return $this->belongsTo(CentreExamen::class);
    }
}
