<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    protected $fillable = [
        'venta_id',
        'fecha_abono',
        'monto_abono',
        'nota'
    ];

    /**
     * Un abono pertenece a una Venta específica.
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}