<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.usuario_externo_estados', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 45);
            $table->string('codigo', 30)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.usuario_externo_estados');
    }
};
