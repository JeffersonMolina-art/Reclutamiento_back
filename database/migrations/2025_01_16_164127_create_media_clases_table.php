<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaClasesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclutamiento.media_clases', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('nombre')->unique('nombre_UNIQUE');
            $table->integer('meses_vigencias')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reclutamiento.media_clases');
    }

}
