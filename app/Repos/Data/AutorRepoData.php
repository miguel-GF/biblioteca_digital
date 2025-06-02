<?php

namespace App\Repos\Data;

use Illuminate\Support\Facades\DB;

class AutorRepoData
{
  public function get(array $filtros)
  {
    $query = DB::table('autores');
    return $query->get()->toArray();
  }
}
