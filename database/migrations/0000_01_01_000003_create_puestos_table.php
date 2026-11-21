<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.puestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 191);
            $table->foreignId('gerencia_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.gerencias');
            $table->string('estado', 255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.puestos');
    }
};
