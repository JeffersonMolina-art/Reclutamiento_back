<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.postulacion_resultados', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('motivo', 255);
            $table->dateTime('fecha_decision');
            $table->integer('postulacion_id')->unique();
            $table->integer('estado_id')->index();
            $table->foreignId('user_id')->index()->constrained('reclutamiento.users');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('postulacion_id')->references('id')->on('reclutamiento.postulaciones');
            $table->foreign('estado_id')->references('id')->on('reclutamiento.postulacion_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.postulacion_resultados');
    }
};
