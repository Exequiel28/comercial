<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('ventas', function (Blueprint $table) {
        $table->id(); // Llave primaria autoincrementable para la venta
        
        // Relación con el cliente usando el DUI como llave foránea
        $table->string('cliente_dui');
        $table->foreign('cliente_dui')->references('dui')->on('clientes')->onDelete('cascade');
        
        $table->date('fecha_venta');
        $table->string('tipo_pago'); // 'contado' o 'credito'
        
        // Usamos decimal(10,2) para manejar dinero de forma exacta
        $table->decimal('monto_total', 10, 2);
        $table->decimal('monto_prima', 10, 2)->default(0.00); // Por defecto 0 si es contado
        $table->decimal('monto_financiar', 10, 2)->default(0.00); // Monto total - Prima
        
        $table->integer('plazo_meses')->default(0); // Plazo elegido (ej: 6, 12, 18 meses)
        $table->string('estado_credito')->default('contado'); // 'contado', 'pendiente', 'pagado'
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
