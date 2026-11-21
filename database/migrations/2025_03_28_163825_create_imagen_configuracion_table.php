<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reclutamiento.imagen_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('tipo_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tipo_id')->references('id')
                ->on('reclutamiento.imagen_configuracion_tipos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.imagen_configuracion');
    }
};
