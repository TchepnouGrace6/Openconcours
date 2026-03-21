<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concours;
use App\Models\CentreExamen;

class CentreExamenConcours extends Model
{
    use HasFactory;

    protected $table = 'centre_examen_concours'; // table pivot

    protected $fillable = [
        'concours_id',
        'centre_examen_id',
    ];

    /**
     * Relation vers le concours
     */
    public function concours()
    {
        return $this->belongsTo(Concours::class);
    }

    /**
     * Relation vers le centre d'examen
     */
    public function centreExamen()
    {
        return $this->belongsTo(CentreExamen::class);
    }
}
