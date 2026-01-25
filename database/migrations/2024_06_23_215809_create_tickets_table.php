<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Tickets', function (Blueprint $table) {
            $table->increments('id_Tickets');
            $table->integer('status')->unsigned();
            $table->dateTime('fechaSolicitud');
            $table->dateTime('fechaAsignacion')->nullable();
            $table->dateTime('fechaDiagnostico')->nullable();
            $table->dateTime('fechaAprobacion')->nullable();
            $table->dateTime('fechaReparacion')->nullable();
            $table->dateTime('fechaFinalizacion')->nullable();
            $table->integer('tecnAsignado')->unsigned();
            $table->integer('cliente')->unsigned();
            $table->integer('equipo')->unsigned();
            $table->timestamps();

            $table->foreign('cliente')->references('id_RS')->on('RazonSocial')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tecnAsignado')->references('id_RS')->on('RazonSocial')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('equipo')->references('id_Equipos')->on('Equipos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Tickets');
    }
};
