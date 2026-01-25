<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Tickets')->insert([
            [
                'status' => 10080, 
                'fechaSolicitud' => now(),
                'fechaAsignacion' => null,
                'fechaDiagnostico' => null,
                'fechaAprobacion' => null,
                'fechaReparacion' => null,
                'fechaFinalizacion' => null,
                'tecnAsignado' => 26, 
                'cliente' => 5, 
                'equipo' => 4, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 10080, 
                'fechaSolicitud' => now(),
                'fechaAsignacion' => null,
                'fechaDiagnostico' => null,
                'fechaAprobacion' => null,
                'fechaReparacion' => null,
                'fechaFinalizacion' => null,
                'tecnAsignado' => 25, 
                'cliente' => 6, 
                'equipo' => 5, 
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);

    }
}
