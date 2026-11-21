<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reclutamiento.login_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Quién
            $table->unsignedBigInteger('usuario_id')->nullable()->index();
            $table->string('usuario_dominio')->nullable();

            // Cómo
            $table->string('metodo');
            $table->boolean('two_factor_used')->default(false);
            $table->unsignedBigInteger('session_id')->nullable()->index();

            // Desde dónde
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_fingerprint', 64)->nullable()->index();
            $table->string('device_id')->nullable();
            $table->boolean('is_new_device')->default(false);

            // Resultado
            $table->string('resultado');
            $table->string('motivo_fallo')->nullable();
            $table->string('severity_level')->default('info');

            // Sesión
            $table->timestamp('logout_at')->nullable();

            $table->timestamps();

            $table->index(['resultado', 'created_at'], 'idx_login_logs_resultado_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclutamiento.login_logs');
    }
};
