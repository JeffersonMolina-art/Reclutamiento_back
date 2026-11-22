<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.entrevistas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('postulacion_id')->index();
            $table->foreignId('entrevistador_id')->index()->constrained('reclutamiento.users');
            $table->integer('estado_id')->index();
            $table->dateTime('fecha_hora_programada');
            $table->string('modalidad', 45);
            $table->string('ubicacion_o_enlace', 500);
            $table->string('observaciones', 255)->nullable();
            $table->enum('tipo_entrevista', ['tecnica', 'recursos_humanos']);
            $table->enum('resultado', ['pendiente', 'aprobado', 'no_aprobado'])->default('pendiente');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('postulacion_id')->references('id')->on('reclutamiento.postulaciones');
            $table->foreign('estado_id')->references('id')->on('reclutamiento.entrevistas_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.entrevistas');
    }
};
