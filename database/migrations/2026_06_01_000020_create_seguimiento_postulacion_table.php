<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.seguimiento_postulacion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('comentario', 255);
            $table->float('porcentaje');
            $table->integer('postulacion_id')->index();
            $table->integer('estado_id')->index();
            $table->foreignId('user_id')->nullable()->index()->constrained('reclutamiento.users');
            $table->float('puntaje_fase')->nullable();
            $table->integer('estado_anterior_id')->nullable()->index();
            $table->dateTime('fecha_cambio')->useCurrent();
            $table->timestamps();

            $table->foreign('postulacion_id')->references('id')->on('reclutamiento.postulaciones');
            $table->foreign('estado_id')->references('id')->on('reclutamiento.postulacion_estado');
            $table->foreign('estado_anterior_id')->references('id')->on('reclutamiento.postulacion_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.seguimiento_postulacion');
    }
};
