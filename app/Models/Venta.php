<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $fillable = [
        'cliente_dui',
        'producto_id', // ⚠️ Mantenido temporalmente para evitar fallos si la columna aún existe
        'fecha_venta',
        'tipo_pago', 
        'monto_total',
        'monto_prima',
        'monto_financiar', 
        'frecuencia_pago',
        'numero_cuotas',
        'estado_credito'
    ];

    /**
     * Una venta pertenece a un Cliente.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_dui', 'dui');
    }

    /**
     * Una venta tiene muchos detalles (múltiples productos)
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    /**
     * Una venta puede tener muchos Abonos.
     */
    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    /**
     * Mantenido por retrocompatibilidad con registros individuales previos
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}