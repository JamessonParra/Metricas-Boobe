<?php

namespace Database\Seeders;

use App\Models\MetricHistoryRun;
use App\Models\Strategy;
use Illuminate\Database\Seeder;

class MetricHistoryRunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear métricas relacionadas con estrategias
         MetricHistoryRun::factory()->count(50)->create();
    }
}
