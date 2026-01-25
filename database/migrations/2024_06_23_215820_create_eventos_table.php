<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Eventos', function (Blueprint $table) {
            $table->increments('id_Eventos');
            $table->integer('usuario')->unsigned();
            $table->integer('tipoEvento')->unsigned();
            $table->string('descripcion', 150)->nullable();
            $table->string('archivo', 255)->nullable();
            $table->dateTime('fechaEvento');
            $table->dateTime('fechaInicio')->nullable();
            $table->dateTime('fechaFin')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->integer('ticket_id')->unsigned();
            $table->timestamps();

            $table->foreign('usuario')->references('id_RS')->on('RazonSocial')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tipoEvento')->references('id_Clasificacion')->on('Clasificacion')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ticket_id')->references('id_Tickets')->on('Tickets')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Eventos');
    }
};
