<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanEstudio extends Model
{
    use HasFactory;

    protected $table = 'plan_estudio'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria

    protected $fillable = [

        'nombre',
        'descripcion',
        'link',
        'id_usuario',
    ];

    // Relación con el modelo Curso
   /* public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }*/
}
