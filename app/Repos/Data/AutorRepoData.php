<?php

namespace App\Repos\Data;

use Illuminate\Support\Facades\DB;

class AutorRepoData
{
  public function get(array $filtros)
  {
    $query = DB::table('autores')->orderBy('autor');
    return $query->get()->toArray();
  }

  public function getById($autorId)
  {
    $query = DB::table('autores')->where('id', $autorId);
    return $query->get()->first();
  }
}
