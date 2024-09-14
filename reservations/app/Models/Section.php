<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order', 'form_id', 'parent_id', 'title_order']; // Ajout de 'title_order' ici

    // Désactiver les timestamps automatiques
    public $timestamps = false;

    // Relation avec le modèle Form
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    // Relation avec le modèle Field
    public function fields()
    {
        return $this->hasMany(Field::class);  // Associe une section à plusieurs champs
    }

    // Relation avec la section parente
    public function parent()
    {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    // Relation avec les sections enfants
    public function children()
    {
        return $this->hasMany(Section::class, 'parent_id');
    }
}
