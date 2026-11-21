<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reclutamiento.bitacoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type', 191);
            $table->unsignedBigInteger('model_id');
            $table->string('seccion', 191);
            $table->string('titulo', 191);
            $table->text('comentario');
            $table->string('icono', 191)->nullable();
            $table->string('color', 191)->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.bitacoras');
    }
};
