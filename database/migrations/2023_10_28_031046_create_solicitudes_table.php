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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('NUE');
            $table->string('ruta_archivo'); // Cambié el nombre del campo
            $table->timestamp('fecha_subida')->nullable();
            $table->foreign('NUE')->references('NUE')->on('agremiados')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
