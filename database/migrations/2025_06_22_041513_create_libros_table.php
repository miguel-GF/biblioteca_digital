<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();

            $table->string('cod_barras')->nullable();
            $table->unsignedBigInteger('autor_id');
            $table->string('autor')->nullable();
            $table->unsignedBigInteger('editorial_id');
            $table->string('editorial')->nullable();
            $table->unsignedBigInteger('area_id');
            $table->string('area')->nullable();
            $table->string('clave')->nullable();
            $table->string('isbn')->nullable();
            $table->text('titulo');
            $table->text('descripcion')->nullable();
            $table->string('anio', 4);
            $table->string('status')->default('activo');
            $table->text('archivo_ruta');
            $table->bigInteger('archivo_tamanio');
            $table->text('archivo_nombre');
            $table->text('archivo_nombre_original');
            $table->text('archivo_mime_type');
            $table->unsignedBigInteger('registro_autor_id')->nullable();
            $table->timestamp('registro_fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
