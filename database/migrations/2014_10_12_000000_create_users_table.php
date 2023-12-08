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
        Schema::create('cuartos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',50);
            $table->unsignedBigInteger('propietario');
            $table->foreign('propietario')->references('id')->on('users');
        });
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->unsignedBigInteger('tipo_dispositivo');
            $table->unsignedBigInteger('cuarto');
            $table->foreign('tipo_dispositivo')->references('id')->on('tipos_dispositivos');
            $table->foreign('cuarto')->references('id')->on('cuartos');
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
