<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'nom',
        'email',
        'password',
        'role',
    ];

    // Cacher le mot de passe
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Vérifier si c'est un admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
