<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // 1. Agregamos la frecuencia de pago
            $table->string('frecuencia_pago')->default('mensual')->after('monto_financiar'); 
            // Valores sugeridos que guardaremos: 'diario', 'semanal', 'quincenal', 'mensual'

            // 2. Renombramos o cambiamos el concepto de 'plazo_meses' a 'numero_cuotas'
            $table->integer('numero_cuotas')->default(0)->after('frecuencia_pago');
            
            // Eliminamos la columna vieja para que no estorbe
            $table->dropColumn('plazo_meses');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Por si alguna vez queremos revertir este cambio
            $table->dropColumn(['frecuencia_pago', 'numero_cuotas']);
            $table->integer('plazo_meses')->default(0)->after('monto_financiar');
        });
    }
};