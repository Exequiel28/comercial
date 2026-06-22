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
    Schema::create('abonos', function (Blueprint $table) {
        $table->id(); // Llave primaria del recibo de abono
        
        // Relación con la tabla ventas
        $table->unsignedBigInteger('venta_id');
        $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
        
        $table->date('fecha_abono');
        $table->decimal('monto_abono', 10, 2); // El dinero en efectivo que entrega
        $table->string('nota')->nullable(); // Una nota opcional (Ej: "Abono puntual", "Pagó hermano")
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonos');
    }
};
