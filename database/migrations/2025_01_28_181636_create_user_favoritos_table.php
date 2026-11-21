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
        Schema::create('reclutamiento.user_favoritos', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('opcion_id');
            $table->integer('orden')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('reclutamiento.users');
            $table->foreign('opcion_id')
                ->references('id')
                ->on('reclutamiento.menu_opciones');

            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('reclutamiento.user_favoritos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['opcion_id']);
        });

        Schema::dropIfExists('reclutamiento.user_favoritos');
    }
};
