<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Departement;

class Ecole extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'ville',
        'pays',
    ];

    // Relation avec les départements
    public function departements()
    {
        return $this->hasMany(Departement::class);
    }
}
