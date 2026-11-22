<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.postulacion_asignaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('bandeja_id')->index();
            $table->integer('estado_id')->index();
            $table->integer('postulacion_id')->index();
            $table->foreignId('users_id')->index()->constrained('reclutamiento.users');
            $table->string('nombre', 45);
            $table->timestamps();


            $table->foreign('bandeja_id')->references('id')->on('reclutamiento.bandeja');
            $table->foreign('postulacion_id')->references('id')->on('reclutamiento.postulaciones');
            $table->foreign('estado_id')->references('id')->on('reclutamiento.postulacion_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.postulacion_asignaciones');
    }
};
