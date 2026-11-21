<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.postulacion_estado', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 45);
            $table->integer('bandeja_id')->index();
            $table->string('codigo', 30)->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bandeja_id')->references('id')->on('reclutamiento.bandeja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.postulacion_estado');
    }
};
