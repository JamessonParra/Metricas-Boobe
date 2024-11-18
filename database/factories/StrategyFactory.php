<?php
namespace Database\Factories;

use App\Models\Strategy;
use Illuminate\Database\Eloquent\Factories\Factory;

class StrategyFactory extends Factory
{
    protected $model = Strategy::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['DESKTOP', 'MOBILE']),
        ];
    }
}
