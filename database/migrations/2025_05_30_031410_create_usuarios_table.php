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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('idusuarios');
            $table->string('correo');
            $table->string('password');
            $table->integer('claveusuario');
            $table->string('nombre');
            $table->string('fechaacceso')->nullable();
            $table->string('horaacceso')->nullable();
            $table->integer('nivel')->nullable();
            $table->string('status')->nullable();
            $table->string('tipo')->nullable();
            $table->string('campus')->nullable();
            $table->string('sist_registros')->nullable();
            $table->string('sist_administrativo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
