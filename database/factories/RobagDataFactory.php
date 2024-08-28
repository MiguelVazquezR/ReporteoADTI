<?php

namespace Database\Factories;

use Carbon\Carbon;
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
            'robag_up_time' => (string) $this->faker->numberBetween(30000, 50000 ), //Tiempo en segundos desde que se encendió el Robag o se reinició el software
            'reason_for_stop_status' => (string) $this->faker->randomElement(['maintenance', 'fault', 'manual_stop']),
            'run_time' => (string) $this->faker->numberBetween(30000, 55000), //Tiempo total de ejecución (ya con pausas) en segundos
            'paused_time' => (string) $this->faker->numberBetween(700, 3600), //Tiempo en pausa en segundos
            'fault_time' => (string) $this->faker->numberBetween(700, 3600), //Tiempo de falla en segundos
            'out_on_film_time' => (string) $this->faker->numberBetween(500, 3600), //Tiempo en estado sin película en segundos
            'last_metal_detect_time' => (string) $this->faker->time(),
            'bags_this_roll' => (string) $this->faker->numberBetween(0, 10000),
            'bags_last_roll' => (string) $this->faker->numberBetween(0, 10000),
            'PISD_reject_count' => (string) $this->faker->numberBetween(0, 1000),
            'scale_good_bags' => (string) $this->faker->numberBetween(0, 10000),
            'scale_overweight_bags' => (string) $this->faker->numberBetween(0, 500),
            'scale_underweight_bags' => (string) $this->faker->numberBetween(0, 500),
            'scale_overscale_count' => (string) $this->faker->numberBetween(0, 1000),
            'scale_warnings' => (string) $this->faker->numberBetween(0, 500),
            'full_bags' => (string) $this->faker->numberBetween(0, 10000), //bolsas llenas
            'empty_bags' => (string) $this->faker->numberBetween(0, 500), //bolsas vacias
            'efficiency_percentage' => (string) $this->faker->randomFloat(2, 70, 100),
            'target_weight' => (string) $this->faker->randomFloat(2, 89, 90), //peso establecido por bolsa en gr. se puede ajustar en la vista
            'total_dump_weight' => (string) $this->faker->randomFloat(2, 1000, 50000),
            'bags_per_minute' => (string) $this->faker->randomFloat(2, 80, 120), //bolsas por minuto, normalmene de 120
            'total_giveaway' => (string) $this->faker->randomFloat(2, 0, 100),
            'giveaway_percentage' => (string) $this->faker->randomFloat(2, 0, 5),
            'total_waste' => (string) $this->faker->randomFloat(2, 0, 1000), //total de bolsas malas
            'total_bags' => (string) $this->faker->numberBetween(10000, 100000), //bolsas totales empacadas
            'mean_weight' => (string) $this->faker->randomFloat(2, 10, 100),
            'standard_deviation' => (string) $this->faker->randomFloat(2, 0, 5),
            'total_dumps' => (string) $this->faker->numberBetween(0, 1000),
            'gas_total' => (string) $this->faker->randomFloat(2, 100, 10000),
            'scale_bpm' => (string) $this->faker->randomFloat(2, 50, 150),
            'heads_per_dump' => (string) $this->faker->numberBetween(1, 10),
            'average_weight_in_ready_heads' => (string) $this->faker->randomFloat(2, 10, 100),
            'short_term_scale_efficiency_percentage' => (string) $this->faker->randomFloat(2, 70, 100),
            'created_at' => $this->randomDateTimeThisMonth(),
            'updated_at' => $this->randomDateTimeThisMonth(),
        ];
    }

    /**
     * Genera una fecha y hora aleatoria dentro del mes en curso.
     */
    private function randomDateTimeThisMonth()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $currentDate = Carbon::now();

        // Genera una fecha aleatoria entre el inicio del mes y hoy
        $randomDate = Carbon::createFromTimestamp(rand($startOfMonth->timestamp, $currentDate->timestamp));

        // Asigna una hora, minutos y segundos aleatorios
        return $randomDate->setTime(
            rand(0, 23),  // Hora aleatoria entre 0 y 23
            rand(0, 59),  // Minutos aleatorios entre 0 y 59
            rand(0, 59)   // Segundos aleatorios entre 0 y 59
        );
    }
}
