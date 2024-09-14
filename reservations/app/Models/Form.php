<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id'];

    // Désactiver les "timestamps" automatiques
    public $timestamps = false;

    // Relation avec le modèle Section
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // Relation avec le modèle Field
    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    // Relation avec le modèle Code
    public function code()
    {
        return $this->hasOne(Code::class);  // Form a un seul Code
    }

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
