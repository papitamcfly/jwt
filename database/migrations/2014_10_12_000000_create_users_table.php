<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipo_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedBigInteger('tipo_usuario')->default(2);
            $table->boolean('is_active')->nullable();
            $table->foreign('tipo_usuario')->references('id')->on('tipo_usuarios');
        });
        Schema::create('tipos_dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
        });
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->unsignedBigInteger('tipo_dispositivo');
            $table->foreign('tipo_dispositivo')->references('id')->on('tipos_dispositivos');
        });
        Schema::create('cuartos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('propietario');
            $table->unsignedBigInteger('dispositivos');
            $table->foreign('propietario')->references('id')->on('users');
            $table->foreign('dispositivos')->references('id')->on('dispositivos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_usuarios');
        Schema::dropIfExists('users');
        Schema::dropIfExists('cuartos');
        Schema::dropIfExists('dispositivos');
        Schema::dropIfExists('tipos_dispositivos');
    }
};
