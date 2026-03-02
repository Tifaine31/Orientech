<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Localisation extends Model // Changez 'localisation' en 'Localisation'
{
    protected $table = 'localisation';
    protected $fillable = ['latitude', 'longitude', 'altitude', 'id_seance', 'id_boitier'];
}
