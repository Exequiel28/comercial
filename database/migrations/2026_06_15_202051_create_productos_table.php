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
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_modelo')->unique(); // Ej: TV-LG55-01
        $table->string('descripcion');             // Ej: Televisor Smart 55 pulgadas
        $table->string('marca');                   // Ej: LG
        $table->decimal('precio', 10, 2);          // Precio de venta
        $table->integer('stock')->default(0);      // Unidades disponibles
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
