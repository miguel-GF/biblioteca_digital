<?php

namespace App\Repos\Data;

use App\Constants;
use Illuminate\Support\Facades\DB;
use stdClass;

class AlumnoRepoData
{
  /**
   * obtenerCalificacionesPorId
   *
   * @param  int $matricula
   * @return stdClass
   */
  public function buscarPorMatricula($matricula)
  {
    $query = DB::table('alumnos as al')
      ->select(
        'al.idalumnos',
        'al.numestudiante',
        'al.nombre'
      )
      ->where('al.numestudiante', $matricula)
      ->whereRaw("LOWER(al.status) = ?", ['activo']);

    return $query->get()->first();
  }
}
