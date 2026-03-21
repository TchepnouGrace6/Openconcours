<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreDepot extends Model
{
    use HasFactory;

    protected $table = 'centre_depot';

    // Champs autorisés en mass assignment
    protected $fillable = [
        'concours_id',
        'nom',
        'adresse',
    ];

    /**
     * 🔹 Relation avec les enrôlements
     * Un centre de dépôt peut avoir plusieurs enrôlements.
     */
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class, 'centre_depot_id');
    }

    public function concours()
    {
        return $this->belongsToMany(Concours::class, 'concours_id');
    }
}
