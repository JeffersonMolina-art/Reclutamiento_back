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
        Schema::create('reclutamiento.plantillas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);

            $table->unsignedBigInteger('tipo_id')->nullable();
            $table->foreign('tipo_id')
                ->references('id')
                ->on('reclutamiento.plantillas_tipos')
                ->onUpdate('no action')
                ->onDelete('no action');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.plantillas');
    }
};
