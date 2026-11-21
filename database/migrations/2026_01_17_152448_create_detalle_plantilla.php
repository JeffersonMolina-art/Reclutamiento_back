<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reclutamiento.detalle_plantilla', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('plantilla_id');
            $table->foreignId('media_id')
                ->constrained('reclutamiento.media')
                ->onDelete('cascade');
            $table->text('variables');
            $table->integer('version');
            $table->text('opciones')->nullable();
            $table->boolean('is_active')->default(false);

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('user_active_id')->nullable();
            $table->timestamp('fecha_activacion_at')->nullable();

            $table->foreign('plantilla_id')
                ->references('id')
                ->on('reclutamiento.plantillas')
                ->onDelete('cascade');

            $table->foreign('user_id', 'fk_detalle_plantilla_user_creador')
                ->references('id')
                ->on('reclutamiento.users');

            $table->foreign('user_active_id', 'fk_detalle_plantilla_user_activador')
                ->references('id')
                ->on('reclutamiento.users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.detalle_plantilla');
    }
};
