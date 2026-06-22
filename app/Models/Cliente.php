<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 👈 Agregado

class Cliente extends Model
{
    use SoftDeletes; // 👈 Agregado

    // 1. Le decimos que la llave primaria no se llama 'id', sino 'dui'
    protected $primaryKey = 'dui';

    // 2. Le avisamos que la llave primaria no es un número autoincrementable
    public $incrementing = false;

    // 3. Le indicamos que el tipo de datos de la llave es un texto (string)
    protected $keyType = 'string';

    // 4. Habilitamos los campos para que Laravel nos permita registrar datos masivamente
    protected $fillable = [
        'dui',
        'nombres',
        'apellidos',
        'direccion',
        'telefono',
        'contacto_referencia'
    ];
}