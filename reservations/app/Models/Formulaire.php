<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulaire extends Model
{
    use HasFactory;

    protected $fillable = ['nom_formulaire', 'date_creation', 'user_id'];

    // Relation avec ChampsFormulaires
    public function champsFormulaires()
    {
        return $this->hasMany(ChampsFormulaire::class, 'formulaire_id');
    }
}
