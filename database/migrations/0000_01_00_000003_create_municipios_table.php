<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.municipios', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo')->nullable();
            $table->string('nombre', 255);
            $table->string('estado', 255);
            $table->foreignId('departamento_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.departamentos');
            $table->integer('existe_agencia')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.municipios');
    }
};
