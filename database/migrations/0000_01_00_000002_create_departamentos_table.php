<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.departamentos', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo')->nullable();
            $table->string('nombre', 191);
            $table->string('estado', 255);
            $table->integer('codigo_bankworks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.departamentos');
    }
};
