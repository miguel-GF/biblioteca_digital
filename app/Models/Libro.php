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
    'sugerido',
    'importancia',
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
    'sugerido' => 'boolean',
    'importancia' => 'integer',
  ];

  /**
   * Libros marcados como sugeridos, ordenados por importancia (mayor primero)
   * y, en caso de empate, por título.
   */
  public function scopeSugeridos($query)
  {
    return $query->where('sugerido', true)
      ->orderByDesc('importancia')
      ->orderBy('titulo');
  }

  /**
   * Datos mínimos que se envían al frontend para pintar un libro.
   */
  public function resumenVista(): array
  {
    return [
      'id' => $this->id,
      'titulo' => $this->titulo,
      'autor' => $this->autor,
      'editorial' => $this->editorial,
      'anio' => $this->anio,
      'descripcion' => $this->descripcion,
      'area' => $this->area,
      'sugerido' => (bool) $this->sugerido,
      'importancia' => (int) $this->importancia,
      'archivo_url' => "/preview-libro/{$this->archivo_ruta}",
      'archivo_ruta' => $this->archivo_ruta,
      'archivo_nombre' => $this->archivo_nombre,
    ];
  }
}
