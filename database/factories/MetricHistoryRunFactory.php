<?php

namespace Database\Factories;

use App\Models\MetricHistoryRun;
use App\Models\Strategy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MetricHistoryRun>
 */
class MetricHistoryRunFactory extends Factory
{
    protected $model = MetricHistoryRun::class;

    public function definition()
    {
        return [
            'url'                   => $this->faker->url(),
            'accessibility_metric'  => $this->faker->numberBetween(0, 100),
            'pwa_metric'            => $this->faker->numberBetween(0, 100),
            'performance_metric'    => $this->faker->numberBetween(0, 100),
            'seo_metric'            => $this->faker->numberBetween(0, 100),
            'best_practices_metric' => $this->faker->numberBetween(0, 100),
            'strategy_id'           => Strategy::inRandomOrder()->first()->id,
            'created_at'            => now(),
            'updated_at'            => now(),
        ];
    }
}
