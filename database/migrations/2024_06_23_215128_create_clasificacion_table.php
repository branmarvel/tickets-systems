<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Clasificacion', function (Blueprint $table) {
            $table->increments('id_Clasificacion');
            $table->string('titulo', 45);
            $table->string('descripcion', 150)->nullable();
            $table->string('icono', 200)->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->timestamps();
        });

        // Establecer el punto de inicio del auto-incremento
        DB::statement('ALTER TABLE Clasificacion AUTO_INCREMENT = 10000;');
    }

    public function down()
    {
        Schema::dropIfExists('Clasificacion');
    }
};
