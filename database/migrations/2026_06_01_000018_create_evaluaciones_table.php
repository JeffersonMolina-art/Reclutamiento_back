<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.evaluaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('postulacion_id')->index();
            $table->foreignId('entrevistador_id')->index()->constrained('reclutamiento.users');
            $table->float('puntuacion');
            $table->string('modalidad', 45);
            $table->dateTime('fecha_evaluacion');
            $table->string('comentarios', 255)->nullable();
            $table->foreignId('users_id')->index()->constrained('reclutamiento.users');
            $table->integer('postulacion_estado_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('postulacion_id')->references('id')->on('reclutamiento.postulaciones');
            $table->foreign('postulacion_estado_id')->references('id')->on('reclutamiento.postulacion_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.evaluaciones');
    }
};
