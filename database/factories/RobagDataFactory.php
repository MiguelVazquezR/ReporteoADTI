<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RobagDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => (string) $this->faker->randomElement(['running', 'stopped', 'paused']),
            'scale_low_product' => (string) $this->faker->boolean(),
            'reset_counters' => (string) $this->faker->boolean(),
            'scale_internal_status' => (string) $this->faker->randomElement(['OK', 'error']),
            'robag_up_time' => (string) $this->faker->numberBetween(1000, 100000),
            'reason_for_stop_status' => (string) $this->faker->randomElement(['maintenance', 'fault', 'manual_stop']),
            'run_time' => (string) $this->faker->numberBetween(100, 10000),
            'paused_time' => (string) $this->faker->numberBetween(0, 5000),
            'fault_time' => (string) $this->faker->numberBetween(0, 5000),
            'out_on_film_time' => (string) $this->faker->numberBetween(0, 5000),
            'last_metal_detect_time' => (string) $this->faker->time(),
            'bags_this_roll' => (string) $this->faker->numberBetween(0, 10000),
            'bags_last_roll' => (string) $this->faker->numberBetween(0, 10000),
            'PISD_reject_count' => (string) $this->faker->numberBetween(0, 1000),
            'scale_good_bags' => (string) $this->faker->numberBetween(0, 10000),
            'scale_overweight_bags' => (string) $this->faker->numberBetween(0, 500),
            'scale_underweight_bags' => (string) $this->faker->numberBetween(0, 500),
            'scale_overscale_count' => (string) $this->faker->numberBetween(0, 1000),
            'scale_warnings' => (string) $this->faker->numberBetween(0, 500),
            'full_bags' => (string) $this->faker->numberBetween(0, 10000),
            'empty_bags' => (string) $this->faker->numberBetween(0, 500),
            'efficiency_percentage' => (string) $this->faker->randomFloat(2, 70, 100),
            'target_weight' => (string) $this->faker->randomFloat(2, 10, 100),
            'total_dump_weight' => (string) $this->faker->randomFloat(2, 1000, 50000),
            'bags_per_minute' => (string) $this->faker->randomFloat(2, 50, 150),
            'total_giveaway' => (string) $this->faker->randomFloat(2, 0, 100),
            'giveaway_percentage' => (string) $this->faker->randomFloat(2, 0, 5),
            'total_waste' => (string) $this->faker->randomFloat(2, 0, 1000),
            'total_bags' => (string) $this->faker->numberBetween(10000, 100000),
            'mean_weight' => (string) $this->faker->randomFloat(2, 10, 100),
            'standard_deviation' => (string) $this->faker->randomFloat(2, 0, 5),
            'total_dumps' => (string) $this->faker->numberBetween(0, 1000),
            'gas_total' => (string) $this->faker->randomFloat(2, 100, 10000),
            'scale_bpm' => (string) $this->faker->randomFloat(2, 50, 150),
            'heads_per_dump' => (string) $this->faker->numberBetween(1, 10),
            'average_weight_in_ready_heads' => (string) $this->faker->randomFloat(2, 10, 100),
            'short_term_scale_efficiency_percentage' => (string) $this->faker->randomFloat(2, 70, 100),
        ];
    }
}
