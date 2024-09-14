<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }
}
