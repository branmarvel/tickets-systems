<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ClasificacionSeeder::class,
            RazonSocialSeeder::class,
            EquiposSeeder::class,
            TicketsSeeder::class,
            
        ]);
    }
}
