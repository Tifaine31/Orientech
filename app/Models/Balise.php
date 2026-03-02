<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balise extends Model
{
    // On indique à Laravel le nom exact de la table dans la BDD
    protected $table = 'balise';

    // On désactive les timestamps si vous ne les gérez pas via Eloquent
    // ou on les laisse si vous avez bien created_at et updated_at
    public $timestamps = true;

    protected $fillable = [
        'tag',
        'lat',
        'lng',
        'alt'
    ];
}
