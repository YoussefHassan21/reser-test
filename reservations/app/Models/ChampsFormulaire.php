<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampsFormulaire extends Model
{
    use HasFactory;

    protected $fillable = ['nom_champ', 'types_champs', 'formulaire_id'];

    public function formulaire()
    {
        return $this->belongsTo(Formulaire::class);
    }
}
