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
        Schema::create('reclutamiento.user_preferencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('ancho_contenido_aplicacion', ['boxed', 'fluid'])->default('boxed');
            $table->enum('aspecto', ['default', 'bordered'])->default('default');
            $table->string('color_primario_tema_light')->default('#237367F0');
            $table->string('color_oscurecimiento_primario_tema_light')->default('#23675DD8');
            $table->string('color_primario_tema_dark')->default('#237367F0');
            $table->string('color_oscurecimiento_primario_tema_dark')->default('#23675DD8');
            $table->boolean('desenfoque_habilitado_barra_navegacion')->default(true);
            $table->enum('navBar_loyout', ['vertical', 'horizontal'])->default('vertical');
            $table->boolean('es_navBar_vertical_semioscura')->default(false);
            $table->boolean('esta_navBar_vertical_colapsada')->default(false);
            $table->enum('tema', ['light', 'dark', 'system'])->default('system');
            $table->enum('tema_color', ['light', 'dark'])->default('dark');
            $table->enum('direccion', ['ltr', 'rtl'])->default('rtl');
            $table->enum('navBar_vertical_tipo', ['sticky', 'static', 'hidden'])->default('sticky');
            $table->enum('navBar_horizontal_tipo', ['sticky', 'static', 'hidden'])->default('sticky');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.user_preferencias');
    }
};
