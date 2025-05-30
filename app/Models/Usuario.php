<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'idusuarios';

    public $timestamps = false; // Si no usas created_at / updated_at

    protected $fillable = [
        'correo',
        'password',
        'claveusuario',
        'nombre',
        'fechaacceso',
        'horaacceso',
        'nivel',
        'status',
        'tipo',
        'campus',
        'sist_registros',
        'sist_administrativo',
    ];

    protected $hidden = ['password'];
    
    public function getAuthIdentifierName()
    {
        return 'idusuarios';
    }
}
