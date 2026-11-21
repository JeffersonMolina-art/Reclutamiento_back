<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuOpcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclutamiento.menu_opciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 120)->nullable();
            $table->string('ruta', 120)->nullable();
            $table->string('params')->nullable();
            $table->string('icono', 125)->nullable();
            $table->string('action', 150)->nullable();
            $table->string('subject', 150)->nullable();
            $table->string('titulo_seccion', 150)->nullable();
            $table->boolean('es_opcion_movil')->default(0);

            $table->integer('orden')->nullable()->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')
                ->references('id')
                ->on('reclutamiento.menu_opciones')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reclutamiento.menu_opciones', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('reclutamiento.menu_opciones');
    }
}
