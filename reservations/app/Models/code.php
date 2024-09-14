<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $table = 'code';  // Nom de la table

    protected $fillable = ['code', 'description', 'user_id', 'type_reservation_id', 'form_id', 'section_id', 'field_id'];  // Champs remplissables

    // Relation avec le modèle Field
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    // Relation avec le modèle Form
    public function form()
    {
        return $this->belongsTo(Form::class);  // Code appartient à Form
    }
}
