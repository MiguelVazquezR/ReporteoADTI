<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RobagDataFactory extends Factory
{
    public function definition(): array
    {
        return [
            'data' => [
                'Estado' => (string) $this->faker->randomElement(['1', '5', '10']),
                'Cantidad baja de bolsas' => (string) $this->faker->randomElement(['6', '1']),
                'Restablecer contadores' => (string) $this->faker->randomElement(['2', '1']),
                'Estado interno de la báscula' => (string) $this->faker->randomElement(['200', '404']),
                'Tiempo de actividad de Robag' => (string) $this->faker->numberBetween(30000, 50000 ),
                'Motivo del estado de parada' => (string) $this->faker->randomElement(['101', '202', '303']),
                'Tiempo de ejecución' => (string) $this->faker->numberBetween(30000, 55000), 
                'Tiempo pausado' => (string) $this->faker->numberBetween(700, 3600),
                'Tiempo de falla' => (string) $this->faker->numberBetween(700, 3600), 
                'Tiempo sin película' => (string) $this->faker->numberBetween(500, 3600), 
                'Tiempo de interlock' => (string) $this->faker->numberBetween(0, 600),
                'Última vez de detección de metales' => (string) $this->faker->numberBetween(0, 900),
                'Bolsas rollo en uso' => (string) $this->faker->numberBetween(0, 10000),
                'Bolsas último rollo' => (string) $this->faker->numberBetween(0, 10000),
                'Recuento de rechazos de PISD' => (string) $this->faker->numberBetween(0, 1000),
                'Bolsas buenas' => (string) $this->faker->numberBetween(0, 10000),
                'Bolsas con excedente de peso' => (string) $this->faker->numberBetween(0, 500),
                'Bolsas de peso bajo' => (string) $this->faker->numberBetween(0, 500),
                'Recuento de sobreescala de báscula' => (string) $this->faker->numberBetween(0, 1000),
                'Advertencias de báscula' => (string) $this->faker->numberBetween(0, 500),
                'Bolsas llenas' => (string) $this->faker->numberBetween(0, 10000),
                'Bolsas vacias' => (string) $this->faker->numberBetween(0, 500),
                'Bolsas de prueba o de ajuste' => (string) $this->faker->numberBetween(0, 300),
                'Bolsas movidas' => (string) $this->faker->numberBetween(0, 600),
                '% Eficiencia de máquina' => (string) $this->faker->randomFloat(2, 70, 100),
                'Peso objetivo (gramos)' => (string) $this->faker->randomFloat(2, 89, 90), //peso establecido por bolsa en gr. se puede ajustar en la vista
                'Peso total de descarga' => (string) $this->faker->randomFloat(2, 1000, 50000),
                'Bolsas por minuto' => (string) $this->faker->randomFloat(2, 80, 120), //bolsas por minuto, normalmene de 120
                'Total regalado (gramos)' => (string) $this->faker->randomFloat(2, 0, 100),
                'Producto ragalado %' => (string) $this->faker->randomFloat(2, 0, 5),
                'Total desechado' => (string) $this->faker->randomFloat(2, 0, 1000), //total de bolsas malas
                'Total de bolsas' => (string) $this->faker->numberBetween(10000, 100000), //bolsas totales empacadas
                'Peso medio' => (string) $this->faker->randomFloat(2, 10, 100),
                'Desviación estándar' => (string) $this->faker->randomFloat(2, -4, 4),
                'Volcados totales' => (string) $this->faker->numberBetween(0, 1000),
                'Gas Total (litros)' => (string) $this->faker->randomFloat(2, 100, 10000),
                'báscula bpm' => (string) $this->faker->randomFloat(2, 50, 150),
                'Cabezas por volcado' => (string) $this->faker->numberBetween(1, 10),
                'Peso medio en cabezas listas' => (string) $this->faker->randomFloat(2, 10, 100),
                'Peso promedio' => (string) $this->faker->randomFloat(2, 50, 100),
                'Eficiencia de báscula a corto plazo (%)' => (string) $this->faker->randomFloat(2, 70, 100),
            ],
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
