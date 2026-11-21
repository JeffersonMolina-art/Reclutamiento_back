<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.vacante_caracteristicas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('vacante_id')->index();
            $table->string('nombre', 255);
            $table->string('valor', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vacante_id')->references('id')->on('reclutamiento.vacantes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.vacante_caracteristicas');
    }
};
