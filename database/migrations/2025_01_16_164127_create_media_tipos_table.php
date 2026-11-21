<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaTiposTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclutamiento.media_tipos', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('nombre')->unique('nombre_UNIQUE');
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
        Schema::drop('reclutamiento.media_tipos');
    }

}
