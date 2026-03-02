<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Boitier extends Model // Changez 'boitier' en 'Boitier'
{
    protected $table = 'boitier';
    protected $fillable = ['mac', 'numero_boitier', 'reseau'];
}
