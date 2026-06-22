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
    Schema::create('detalle_ventas', function (Blueprint $table) {
        $table->id();
        // Relación con la cabecera de la venta
        $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
        // Relación con el producto vendido
        $table->foreignId('producto_id')->constrained('productos');
        
        // Guardamos histórico de precios y cantidades de ese momento exacto
        $table->integer('cantidad');
        $table->decimal('precio_unitario', 10, 2);
        $table->decimal('subtotal', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
