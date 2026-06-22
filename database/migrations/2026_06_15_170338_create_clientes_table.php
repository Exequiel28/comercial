<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   /*public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }*/

    public function up(): void
{
    Schema::create('clientes', function (Blueprint $table) {
        // Definimos el DUI como string, llave primaria y único
        $table->string('dui')->primary(); 
        
        $table->string('nombres');
        $table->string('apellidos');
        $table->text('direccion'); // Usamos text por si la dirección es muy larga
        $table->string('telefono');
        $table->text('contacto_referencia'); // Para guardar nombre y teléfono de su referencia
        
        $table->timestamps(); // Esto crea las columnas created_at y updated_at automáticamente
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
