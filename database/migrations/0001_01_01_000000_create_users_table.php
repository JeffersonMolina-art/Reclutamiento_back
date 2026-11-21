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
        Schema::create('reclutamiento.users', function (Blueprint $table) {
            $table->id();
            $table->string('usuario_dominio', 191)
                ->unique();

            $table->string('nombre', 191);
            $table->string('apellido', 191);
            $table->string('email', 191)->nullable();
            $table->string('password', 191);
            $table->enum('estado', ['Activo', 'Inactivo', 'Cancelado'])->nullable();

            $table->foreignId('puesto_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.puestos');

            $table->foreignId('unidad_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.unidads');

            $table->foreignId('region_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.regiones');

            $table->foreignId('gerencia_id')
                ->nullable()
                ->index()
                ->constrained('reclutamiento.gerencias');

            $table->timestamp('password_changed_at')->nullable();
            $table->dateTime('ultimo_login_el')->nullable();
            $table->string('ultimo_login_ip', 191)->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->boolean('two_factor_habilitado')->nullable();
            $table->boolean('two_factor_pending')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->enum('ultima_autenticacion', ['azure', 'credenciales'])->default('credenciales');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reclutamiento.password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('reclutamiento.sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclutamiento.users', function (Blueprint $table) {
            $table->dropForeign(['sigua_id']);
            $table->dropForeign(['asociado_id']);
            $table->dropForeign(['puesto_id']);
            $table->dropForeign(['unidad_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['gerencia_id']);
        });

        Schema::dropIfExists('reclutamiento.users');
        Schema::dropIfExists('reclutamiento.password_reset_tokens');
        Schema::dropIfExists('reclutamiento.sessions');
    }
};
