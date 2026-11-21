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
        Schema::create('reclutamiento.configuracions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->text('valor');
            $table->boolean('cifrado')->default(0);
            $table->enum('tipo', ['string', 'integer', 'float', 'boolean']);
            $table->char('grupo', 20)->default('general');
            $table->text('descripcion');
            $table->timestamps(7);
            $table->dateTime('deleted_at', 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.configuracions');
    }
};
