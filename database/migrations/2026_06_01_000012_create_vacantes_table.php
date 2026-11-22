<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.vacantes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('estado_id')->index();
            $table->foreignId('puesto_id')->index()->constrained('reclutamiento.puestos');
            $table->foreignId('unidad_id')->index()->constrained('reclutamiento.unidads');
            $table->integer('sede_id')->index();
            $table->string('nombre', 255);
            $table->text('descripcion');
            $table->integer('cantidad_plazas');
            $table->foreignId('user_id')->index()->constrained('reclutamiento.users');
            $table->date('fecha_publicacion')->nullable();
            $table->foreignId('user_encargado')->nullable()->index()->constrained('reclutamiento.users');
            $table->enum('modalidad_trabajo', ['presencial', 'remoto', 'hibrido']);
            $table->decimal('salario_minimo', 12, 2);
            $table->decimal('salario_maximo', 12, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('reclutamiento.vacantes_estados');
            $table->foreign('sede_id')->references('id')->on('reclutamiento.sede');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.vacantes');
    }
};
