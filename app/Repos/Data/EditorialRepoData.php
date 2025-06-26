<?php

namespace App\Repos\Data;

use Illuminate\Support\Facades\DB;

class EditorialRepoData
{
  public function get(array $filtros)
  {
    $query = DB::table('editoriales');
    return $query->get()->toArray();
  }

  public function getById($editorialId)
  {
    $query = DB::table('editoriales')->where('id', $editorialId);
    return $query->get()->first();
  }
}
