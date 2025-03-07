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
        if (!Schema::hasTable('historial_permisos')) {
            Schema::create('historial_permisos', function (Blueprint $table) {
                $table->id();
                $table->string('cedula', 20);
                $table->string('nombre_completo', 255);
                $table->string('correo_electronico', 255);
                $table->dateTime('fecha_permiso')->default(now());
                $table->string('motivo', 255);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_permisos');
    }
};
