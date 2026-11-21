<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclutamiento.user_externos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('email', 255)->unique();
            $table->string('telefono', 45)->nullable();
            $table->string('password', 255);
            $table->integer('estado_id')->index();
            $table->dateTime('email_verified_at')->nullable();
            $table->dateTime('telefono_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('reclutamiento.usuario_externo_estados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.user_externos');
    }
};
