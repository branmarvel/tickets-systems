<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Equipos', function (Blueprint $table) {
            $table->increments('id_Equipos');
            $table->string('descripcion', 45)->nullable();
            $table->integer('marca')->unsigned();
            $table->string('modelo', 90);
            $table->string('nroSerie', 45);
            $table->integer('linea')->unsigned()->nullable();
            $table->integer('tipoEquipo')->unsigned();
            $table->decimal('precio', 10, 2)->nullable();
            $table->integer('motivo')->unsigned();
            $table->integer('id_RS')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('id_RS')->references('id_RS')->on('RazonSocial')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('marca')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('linea')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tipoEquipo')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('motivo')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Equipos');
    }
};
