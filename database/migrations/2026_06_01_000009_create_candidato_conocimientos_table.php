<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.candidato_conocimientos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('candidato_id')->index();
            $table->string('descripcion', 255);
            $table->string('nivel', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('candidato_id')->references('id')->on('reclutamiento.candidato');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.candidato_conocimientos');
    }
};
