<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false; // Si no tienes campos de timestamp

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'fecha_nacimiento',
        'rol_id'
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Método para indicar a Laravel cuál es el campo de contraseña
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Relación con el rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
    // Relación con el modelo Compra
public function compras()
{
    return $this->hasMany(Compra::class, 'usuario_id');
}

// Relación con el modelo Calificacion
public function calificaciones()
{
    return $this->hasMany(Calificacion::class, 'usuario_id');
}
public function suscripciones()
{
    return $this->hasMany(Suscripcion::class, 'consumidor_id');
}
public function suscripcion()
{
    return $this->hasOne(Suscripcion::class, 'consumidor_id');
}


    // Accessor para verificar si el usuario está suscrito


// Verifica si el usuario ha comprado un curso
public function haCompradoCurso($cursoId)
{
    return $this->compras()->where('curso_id', $cursoId)->exists();
}

// Verifica si el usuario está suscrito
public function getEsSuscriptorAttribute()
{
    return $this->suscripcion && $this->suscripcion->estado === 'activo';
}

public function evaluations(): HasMany
{
    return $this->hasMany(Evaluation::class, 'user_id');
}


}
