<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.candidato_formacion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('candidato_id')->index();
            $table->string('nombre', 255);
            $table->string('titulo_carrera', 255)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('candidato_id')->references('id')->on('reclutamiento.candidato');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.candidato_formacion');
    }
};
