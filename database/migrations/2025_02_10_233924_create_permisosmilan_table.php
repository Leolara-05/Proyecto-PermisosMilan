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
        Schema::create('permisosmilan', function (Blueprint $table) {
            $table->id();
            $table->string('cedula');
            $table->string('nombre');
            $table->string('cargo');
            $table->date('fecha_autorizacion');
            $table->dateTime('desde');
            $table->dateTime('hasta');
            $table->text('motivo_permiso');
            $table->boolean('descontable')->default(false);
            $table->text('observaciones')->nullable();
            $table->string('documento')->nullable();
            $table->boolean('autorizado')->nullable();
            $table->string('firma_trabajador')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisosmilan');
    }
};
