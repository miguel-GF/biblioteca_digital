<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'cat_categorias';

    protected $fillable = ['nombre'];

    public $timestamps = true;

    const UPDATED_AT = null; // solo queremos que se guarde created_at
}
