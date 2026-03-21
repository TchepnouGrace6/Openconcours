<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'action',
        'details',
        'ip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
?>