<?php
// app/Models/TypeReservation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeReservation extends Model
{
  protected $table = 'types_reservation';

    use HasFactory;

    protected $fillable = ['name', 'description', 'form_id', 'code_id'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function code()
    {
        return $this->belongsTo(Code::class);
    }
}
