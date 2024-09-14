<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'order', 'section_id', 'form_id'];

    // Désactiver les timestamps automatiques
    public $timestamps = false;

    // Relation avec le modèle Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Relation avec le modèle Form
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    // Relation avec le modèle Code
    public function code()
    {
        return $this->hasOne(Code::class);
    }
}
