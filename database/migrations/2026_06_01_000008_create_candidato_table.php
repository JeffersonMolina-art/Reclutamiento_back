<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.candidato', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->unique();
            $table->string('primer_nombre', 45);
            $table->string('segundo_nombre', 45)->nullable();
            $table->string('tercer_nombre', 45)->nullable();
            $table->string('primer_apellido', 45);
            $table->string('segundo_apellido', 45)->nullable();
            $table->string('dpi', 13)->nullable();
            $table->string('correo_contacto', 100);
            $table->string('telefono_contacto', 12);
            $table->date('fecha_nacimiento');
            $table->decimal('expectativa_salarial', 12, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('reclutamiento.user_externos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.candidato');
    }
};
