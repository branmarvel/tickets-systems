<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('RazonSocial', function (Blueprint $table) {
            $table->increments('id_RS');
            $table->string('nombreCompleto', 45);
            $table->integer('tipoPersona')->unsigned();
            $table->string('nroIdentificacion', 15);
            $table->string('telefono', 15);
            $table->string('email', 45)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->integer('rol')->unsigned();
            $table->string('contraseña', 150)->nullable();
            $table->timestamps();

            $table->foreign('tipoPersona')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rol')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('RazonSocial');
    }
};
