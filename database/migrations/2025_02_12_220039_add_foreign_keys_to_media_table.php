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
        Schema::table('reclutamiento.media', function (Blueprint $table) {
            $table->foreign('gerencia_id')->references('id')->on('reclutamiento.gerencias')->onDelete('set null');
            $table->foreign('region_id')->references('id')->on('reclutamiento.regiones')->onDelete('set null');
            $table->foreign('unidad_id')->references('id')->on('reclutamiento.unidads')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('reclutamiento.users')->onDelete('set null');
            $table->foreign('clase_id')->references('id')->on('reclutamiento.media_clases')->onDelete('set null');
            $table->foreign('tipo_id')->references('id')->on('reclutamiento.media_tipos')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclutamiento.media', function (Blueprint $table) {
            $table->dropIndex('idx_media_gerencia_id');
//            $table->dropIndex('idx_media_region_id');
//            $table->dropIndex('idx_media_unidad_id');
//            $table->dropIndex('idx_media_user_id');
//            $table->dropIndex('idx_media_asociado_id');
//            $table->dropIndex('idx_media_clase_id');
//            $table->dropIndex('idx_media_tipo_id');
//            $table->dropColumn('gerencia_id');
//            $table->dropColumn('region_id');
//            $table->dropColumn('unidad_id');
//            $table->dropColumn('user_id');
//            $table->dropColumn('asociado_id');
//            $table->dropColumn('clase_id');
//            $table->dropColumn('tipo_id');
//            $table->dropSoftDeletes();
//            $table->dropForeign(['gerencia_id']);
//            $table->dropForeign(['region_id']);
//            $table->dropForeign(['unidad_id']);
//            $table->dropForeign(['user_id']);
//            $table->dropForeign(['asociado_id']);
//            $table->dropForeign(['clase_id']);
//            $table->dropForeign(['tipo_id']);
        });
    }
};
