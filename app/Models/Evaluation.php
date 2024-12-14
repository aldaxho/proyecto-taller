<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'materia', 'nivel', 'resultado', 'permiso'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
   
}
