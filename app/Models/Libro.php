<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Libro extends Authenticatable
{
  use Notifiable;

  protected $table = 'libros';
  protected $primaryKey = 'id';

  public $timestamps = false; // Si no usas created_at / updated_at

  protected $fillable = [
    'cod_barras',
    'autor_id',
    'autor',
    'editorial_id',
    'editorial',
    'area_id',
    'area',
    'clave',
    'isbn',
    'titulo',
    'descripcion',
    'anio',
    'status',
    'archivo_ruta',
    'archivo_tamanio',
    'archivo_nombre',
    'archivo_nombre_original',
    'archivo_mime_type',
    'registro_autor_id',
    'registro_fecha'
  ];

  // Si quieres que al acceder a 'registro_fecha' se convierta a instancia de Carbon automáticamente
  protected $casts = [
    'registro_fecha' => 'datetime',
  ];
}
