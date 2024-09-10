<?php

namespace App\Services;

use App\Models\MachineVariable;
use App\Models\ModbusConfiguration;
use ModbusTcpClient\Exception\ModbusException;
use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersResponse;
use ModbusTcpClient\Packet\ResponseFactory;
use ModbusTcpClient\Utils\Endian;

class ModbusService
{
    protected $connection;
    public $machine;

    public function __construct($machine)
    {
        $this->machine = $machine;
        // Obtener la configuración del PLC desde la base de datos
        $config = ModbusConfiguration::firstWhere('machine', $machine);

        if ($config) {
            Endian::$defaultEndian = Endian::BIG_ENDIAN_LOW_WORD_FIRST; // set default (global) endian used for parsing data

            $this->connection = BinaryStreamConnection::getBuilder()
                ->setHost($config->host)
                ->setPort($config->port)
                ->setConnectTimeoutSec(1.5) // timeout when establishing connection to the server
                ->setWriteTimeoutSec(0.5) // timeout when writing/sending packet to the server
                ->setReadTimeoutSec(0.3) // timeout when waiting response from server
                ->build();
        } else {
            throw new \Exception('No hay ninguna configuración Modbus registrada.');
        }
    }

    public function getMachineData()
    {
        try {
            // Obtener las variables de la base de datos para la máquina específica
            $variables = MachineVariable::where('machine_name', $this->machine)->get();

            $data = [];

            // Crear una solicitud de lectura para cada dirección de variable
            foreach ($variables as $variable) {
                $unitID = 1;
                $packet = new ReadHoldingRegistersRequest($variable->variable_address, $variable->words, $unitID);

                $binaryData = $this->connection->connect()->sendAndReceive($packet);
                $response = ResponseFactory::parseResponseOrThrow($binaryData);
                //obtener valor depende del tipo y las palabras leidas
                if ($variable->words == 1) {
                    if ($variable->type == 'int') {
                        $value = $response->getWordAt($variable->variable_address)->getInt16();
                    } else if ($variable->type == 'uint') {
                        $value = $response->getWordAt($variable->variable_address)->getUInt16();
                    }
                } else {
                    if ($variable->type == 'int') {
                        $value = $response->getDoubleWordAt($variable->variable_address)->getInt32();
                    } else if ($variable->type == 'float') {
                        $value = $response->getDoubleWordAt(0)->getFloat();
                        dd($value);
                    } else if ($variable->type == 'uint') {
                        $value = $response->getDoubleWordAt($variable->variable_address)->getUInt32();
                    }
                }
                // Guardar el valor obtenido con el nombre de la variable original
                $data[$variable->variable_original_name] = $value;
            }

            return $data;
        } catch (ModbusException $e) {
            // datos de prueba
            // return [
            //     "status" => "running",
            //     "scale_low_product" => "normal",
            //     "reset_counters" => 0,
            //     "scale_internal_status" => "normal",
            //     "robag_up_time" => 5000,
            //     "reason_for_stop_status" => "none",
            //     "run_time" => 4500,
            //     "paused_time" => 300,
            //     "fault_time" => 200,
            //     "out_on_film_time" => 100,
            //     "last_metal_detect_time" => 1200,
            //     "bags_this_roll" => 2000,
            //     "bags_last_roll" => 1950,
            //     "PISD_reject_count" => 15,
            //     "scale_good_bags" => 1800,
            //     "scale_overweight_bags" => 10,
            //     "scale_underweight_bags" => 5,
            //     "scale_overscale_count" => 2,
            //     "scale_warnings" => 1,
            //     "full_bags" => 1750,
            //     "empty_bags" => 50,
            //     "efficiency_percentage" => 95.5,
            //     "target_weight" => 50.0,
            //     "total_dump_weight" => 90000.0,
            //     "bags_per_minute" => 120,
            //     "total_giveaway" => 1500.0,
            //     "giveaway_percentage" => 1.67,
            //     "total_waste" => 100.0,
            //     "total_bags" => 1800,
            //     "mean_weight" => 50.05,
            //     "standard_deviation" => 0.02,
            //     "total_dumps" => 1800,
            //     "gas_total" => 500,
            //     "scale_bpm" => 110,
            //     "heads_per_dump" => 10,
            //     "average_weight_in_ready_heads" => 50.1,
            //     "short_term_scale_efficiency_percentage" => 93.0
            // ];
            return ['error' => $e->getMessage()];
        } finally {
            $this->connection->close();
        }
    }
}
