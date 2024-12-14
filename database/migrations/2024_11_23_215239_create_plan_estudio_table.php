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
        Schema::create('plan_estudio', function (Blueprint $table) {
            $table->id(); // Crea un campo auto-incremental 'id' como clave primaria
            $table->string('nombre'); // Campo para el nombre del plan de estudio
            $table->text('descripcion'); // Campo para la descripción del plan de estudio
            $table->string('link')->nullable(); // Campo para el link, puede ser nulo
            $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade'); // Relación con la tabla 'usuarios' (asumí que la tabla se llama 'usuarios')
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_estudio');
    }
};
