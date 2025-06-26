<?php

namespace App\Repos\Data;

use Illuminate\Support\Facades\DB;

class AreaRepoData
{
  public function get(array $filtros)
  {
    $query = DB::table('areas');
    return $query->get()->toArray();
  }

  public function getById($areaId)
  {
    $query = DB::table('areas')->where('id', $areaId);
    return $query->get()->first();
  }
}
