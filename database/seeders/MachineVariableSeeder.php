<?php

namespace Database\Seeders;

use App\Models\MachineVariable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MachineVariableSeeder extends Seeder
{
    public function run(): void
    {
        $robagVariables = [
            [
                "variable_name" => "Estado de la máquina",
                "variable_original_name" => "status",
                "variable_description" => "Estado",
                "variable_address" => "500",
            ],
            [
                "variable_name" => "Cantidad baja de bolsas",
                "variable_original_name" => "scale_low_product",
                "variable_description" => "0-normal/1-baja señal de producto desde la báscula",
                "variable_address" => "502",
            ],
            [
                "variable_name" => "Restablecer contadores",
                "variable_original_name" => "reset_counters",
                "variable_description" => "Parámetro grabable para restablecer contadores",
                "variable_address" => "504",
            ],
            [
                "variable_name" => "Estado interno de la báscula",
                "variable_original_name" => "scale_internal_status",
                "variable_description" => "Estado de la báscula Selección de información",
                "variable_address" => "511",
            ],
            [
                "variable_name" => "Tiempo de actividad de Robag",
                "variable_original_name" => "robag_up_time",
                "variable_description" => "Tiempo en segundos desde que se encendió el Robag o se reinició el software",
                "variable_address" => "3004",
            ],
            [
                "variable_name" => "Motivo del estado de parada",
                "variable_original_name" => "reason_for_stop_status",
                "variable_description" => "Requiere configuración adicional en Robag / El código de motivo se coloca en este registro",
                "variable_address" => "3006",
            ],
            [
                "variable_name" => "Tiempo de interlock",
                "variable_original_name" => "interlock_time",
                "variable_description" => "Tiempo en funcionamiento pero pausa porque un usuario esta manipulando la máquina",
                "variable_address" => "3008",
            ],
            [
                "variable_name" => "Tiempo de ejecución",
                "variable_original_name" => "run_time",
                "variable_description" => "Tiempo en ejecución",
                "variable_address" => "3010",
            ],
            [
                "variable_name" => "Tiempo pausado",
                "variable_original_name" => "paused_time",
                "variable_description" => "Tiempo pausado",
                "variable_address" => "3012",
            ],
            [
                "variable_name" => "Tiempo de falla",
                "variable_original_name" => "fault_time",
                "variable_description" => "Tiempo de falla",
                "variable_address" => "3014",
            ],
            [
                "variable_name" => "Tiempo sin película",
                "variable_original_name" => "out_of_film_time",
                "variable_description" => "Tiempo en estado sin película",
                "variable_address" => "3016",
            ],
            [
                "variable_name" => "Última vez de detección de metales",
                "variable_original_name" => "last_metal_detect_time",
                "variable_description" => "El tiempo de espera por detección de metales activado. Esto muestra cuánto tiempo ha pasado desde la última detección de metales.",
                "variable_address" => "3018",
            ],
            [
                "variable_name" => "Bolsas rollo en uso",
                "variable_original_name" => "bags_this_roll",
                "variable_description" => "Bolsas hechas desde el último cambio de película.",
                "variable_address" => "3020",
            ],
            [
                "variable_name" => "Bolsas último rollo",
                "variable_original_name" => "bags_last_roll",
                "variable_description" => "Bolsas hechas con el último rollo de película.",
                "variable_address" => "3022",
            ],
            [
                "variable_name" => "Recuento de rechazos de PISD",
                "variable_original_name" => "PISD_reject_count",
                "variable_description" => "Número de bolsas llenas rechazadas por Producto en Detector de Sello (PISD)",
                "variable_address" => "3032",
            ],
            [
                "variable_name" => "Bolsas buenas",
                "variable_original_name" => "scale_good_bags",
                "variable_description" => "Ciclos de creación de bolsas",
                "variable_address" => "3040",
            ],
            [
                "variable_name" => "Bolsas con excedente de peso",
                "variable_original_name" => "scale_overweight_bags",
                "variable_description" => "Ciclos perdidos debido al exceso de peso",
                "variable_address" => "3042",
            ],
            [
                "variable_name" => "Bolsas de peso bajo",
                "variable_original_name" => "scale_underweight_bags",
                "variable_description" => "Ciclos perdidos debido a la falta de peso",
                "variable_address" => "3044",
            ],
            [
                "variable_name" => "Recuento de sobreescala de báscula",
                "variable_original_name" => "scale_overscale_count",
                "variable_description" => "Volcado hecho.  1 cubeta > Objetivo + Límite alto",
                "variable_address" => "3046",
            ],
            [
                "variable_name" => "Advertencias de báscula",
                "variable_original_name" => "scale_warnings",
                "variable_description" => "Advertencias de báscula bit a bit",
                "variable_address" => "3052",
            ],
            [
                "variable_name" => "Bolsas llenas",
                "variable_original_name" => "full_bags",
                "variable_description" => "Número de bolsas llenas",
                "variable_address" => "5000",
            ],
            [
                "variable_name" => "Bolsas vacias",
                "variable_original_name" => "empty_bags",
                "variable_description" => "Número de bolsas vacias",
                "variable_address" => "5002",
            ],
            [
                "variable_name" => "Bolsas de prueba o de ajuste",
                "variable_original_name" => "setup_bags",
                "variable_description" => "Una bolsa de prueba es cuando la película es movida a una posición correcta",
                "variable_address" => "5004",
            ],
            [
                "variable_name" => "Bolsas movidas",
                "variable_original_name" => "jogged_bags",
                "variable_description" => "Número de bolsas movidas",
                "variable_address" => "5006",
            ],
            [
                "variable_name" => "% Eficiencia",
                "variable_original_name" => "efficiency_percentage",
                "variable_description" => "Valor filtrado que representa el número de volcados completos desde la báscula.",
                "variable_address" => "5008",
            ],
            [
                "variable_name" => "Peso objetivo (gramos)",
                "variable_original_name" => "target_weight",
                "variable_description" => "Configuración del peso del paquete",
                "variable_address" => "5010",
            ],
            [
                "variable_name" => "Peso total de descarga",
                "variable_original_name" => "total_dump_weight",
                "variable_description" => "Peso total de todos los volcados de bolsas llenas desde que Robag se enciende o se limpian los contadores",
                "variable_address" => "5012",
            ],
            [
                "variable_name" => "Bolsas por minuto",
                "variable_original_name" => "bags_per_minute",
                "variable_description" => "Punto de ajuste de las bolsas por minuto",
                "variable_address" => "5016",
            ],
            [
                "variable_name" => "Total regalado (gramos)",
                "variable_original_name" => "total_giveaway",
                "variable_description" => "Peso total regalado desde que Robag enciende o limpia los contadores. (producto regalado porque excede el peso objetivo)",
                "variable_address" => "5022",
            ],
            [
                "variable_name" => "Producto ragalado %",
                "variable_original_name" => "giveaway_percentage",
                "variable_description" => "Total regalado / Peso total de descarga",
                "variable_address" => "5024",
            ],
            [
                "variable_name" => "Total desechado -recuento-",
                "variable_original_name" => "total_waste",
                "variable_description" => "Número total de bolsas desperdiciadas.   movidas + Vacias + Bolsas de configuración inicial o de prueba",
                "variable_address" => "5038",
            ],
            [
                "variable_name" => "Total de bolsas -recuento-",
                "variable_original_name" => "total_bags",
                "variable_description" => "Número total de bolsas fabricadas.   Bolsas llenas + bolsas desperdiciadas",
                "variable_address" => "5040",
            ],
            [
                "variable_name" => "Peso medio",
                "variable_original_name" => "mean_weight",
                "variable_description" => "Peso medio de las bolsas realizadas desde la última puesta a cero de los contadores",
                "variable_address" => "5042",
            ],
            [
                "variable_name" => "Desviación estándar",
                "variable_original_name" => "standard_deviation",
                "variable_description" => "La desviación estándar de los pesos de los productos descartados de la báscula desde el último reinicio de los contadores.",
                "variable_address" => "5044",
            ],
            [
                "variable_name" => "Volcados totales",
                "variable_original_name" => "total_dumps",
                "variable_description" => "El número total de volcados de la báscula.",
                "variable_address" => "5046",
            ],
            [
                "variable_name" => "Gas Total (litros)",
                "variable_original_name" => "gas_total",
                "variable_description" => "Control de flujo de gas. Litros totales de nitrógeno utilizados",
                "variable_address" => "5080",
            ],
            [
                "variable_name" => "báscula bpm",
                "variable_original_name" => "scale_bpm",
                "variable_description" => "bolsas por minuto",
                "variable_address" => "5086",
            ],
            [
                "variable_name" => "Cabezas por volcado",
                "variable_original_name" => "heads_per_dump",
                "variable_description" => "Número promedio de cabezas por volcado",
                "variable_address" => "5116",
            ],
            [
                "variable_name" => "Peso medio en cabezas listas",
                "variable_original_name" => "average_weight_in_ready_heads",
                "variable_description" => "Peso medio en las cubetas disponibles para combinaciones.",
                "variable_address" => "5112",
            ],
            [
                "variable_name" => "Peso promedio",
                "variable_original_name" => "average_weight",
                "variable_description" => "Peso promedio en las cubetas disponible para combinaciones",
                "variable_address" => "5122",
            ],
            [
                "variable_name" => "Eficiencia de báscula a corto plazo (%)",
                "variable_original_name" => "short_term_scale_efficiency_percentage)",
                "variable_description" => "Eficiencia en una escala de tiempo más corta",
                "variable_address" => "5124",
            ],
        ];

        foreach ($robagVariables as $value) {
            MachineVariable::create($value + ['machine_name' => 'Robag']);
        }
    }
}
