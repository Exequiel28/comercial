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
        Schema::table('clientes', function (Blueprint $blueprint) {
            // 👈 Esto agregará la columna 'deleted_at' al final de la tabla
            $blueprint->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $blueprint) {
            // 👈 Esto eliminará la columna si en algún momento haces un rollback
            $blueprint->dropSoftDeletes(); 
        });
    }
};