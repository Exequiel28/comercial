<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Importamos el Trait

class Producto extends Model
{
    use SoftDeletes; // 2. Activamos el borrado lógico

    protected $fillable = ['codigo_modelo', 'descripcion', 'marca', 'precio', 'stock'];

    // 3. Indicamos que trate a 'deleted_at' como una fecha (opcional pero recomendado)
    protected $dates = ['deleted_at']; 
}