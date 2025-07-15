<?php

namespace App\Repos\Data;

use Illuminate\Support\Facades\DB;

class AreaRepoData
{
  public function get(array $filtros)
  {
    $query = DB::table('areas')->orderBy('area');
    if (!empty($filtros['destacada']) && $filtros['destacada'] == 'si') {
      $query->where('destacada', true);
    }
    return $query->get()->toArray();
  }

  public function getById($areaId)
  {
    $query = DB::table('areas')->where('id', $areaId);
    return $query->get()->first();
  }
}
