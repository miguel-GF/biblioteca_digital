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
}
