<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.postulaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('vacante_id')->index();
            $table->integer('candidato_id')->index();
            $table->integer('estado_id')->index();
            $table->date('fecha_postulacion');
            $table->enum('fuente_reclutamiento', ['linkedin', 'referido', 'portal_empleo', 'otro']);
            $table->timestamps();

            $table->unique(['vacante_id', 'candidato_id'], 'uq_postulaciones_vacante_candidato');
            $table->index(['vacante_id', 'estado_id'], 'idx_postulaciones_vacante_estado');

            $table->foreign('vacante_id')->references('id')->on('reclutamiento.vacantes');
            $table->foreign('candidato_id')->references('id')->on('reclutamiento.candidato');
            $table->foreign('estado_id')->references('id')->on('reclutamiento.postulacion_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.postulaciones');
    }
};
