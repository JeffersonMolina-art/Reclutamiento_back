<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.unidads', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 191)->nullable();
            $table->string('nombre', 191);
            $table->text('emails')->nullable();
            $table->string('estado', 255);
            $table->foreignId('regiones_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.regiones');
            $table->foreignId('gerencia_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.gerencias');
            $table->unsignedBigInteger('jefe_id')->nullable()->index();
            $table->unsignedBigInteger('auxiliar_id')->nullable()->index();
            $table->foreignId('municipio_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.municipios');
            $table->foreignId('departamento_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.departamentos');
            $table->string('latitud', 191)->nullable();
            $table->string('longitud', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.unidads');
    }
};
