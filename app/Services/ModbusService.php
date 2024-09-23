<?php

namespace App\Services;

use App\Models\MachineVariable;
use App\Models\ModbusConfiguration;
use ModbusTcpClient\Exception\ModbusException;
use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ResponseFactory;
use ModbusTcpClient\Utils\Endian;

class ModbusService
{
    protected $connection;
    protected $machine;

    public function __construct($machine)
    {
        $this->machine = $machine;
        $this->initializeConnection();
    }

    protected function initializeConnection()
    {
        $config = ModbusConfiguration::firstWhere('machine', $this->machine);

        if (!$config) {
            throw new \Exception('No hay ninguna configuración Modbus registrada.');
        }

        Endian::$defaultEndian = Endian::BIG_ENDIAN_LOW_WORD_FIRST;

        $this->connection = BinaryStreamConnection::getBuilder()
            ->setHost($config->host)
            ->setPort($config->port)
            ->setConnectTimeoutSec(1.5)
            ->setWriteTimeoutSec(0.5)
            ->setReadTimeoutSec(0.3)
            ->build();
    }

    protected function parseVariable($response, $variable)
    {
        $address = 0;

        // Procesar variables en función del número de palabras y tipo de variable
        if ($variable->words == 1) {
            if ($variable->type == 'uint') {
                return $response->getWordAt($address)->getUInt16();
            } elseif ($variable->type == 'int') {
                return $response->getWordAt($address)->getInt16();
            } elseif ($variable->type == 'byte') {
                return $response->getWordAt($address)->getLowByteAsInt();
                // return $response->getWordAt($address)->getHighByteAsInt();
            }
        }

        if ($variable->type == 'float') {
            return $response->getDoubleWordAt($address)->getFloat();
        }

        return $variable->type == 'uint'
            ? $response->getDoubleWordAt($address)->getUInt32()
            : $response->getDoubleWordAt($address)->getInt32();
    }

    public function getMachineData()
    {
        try {
            // Obtener las variables de caché, si no están, consultarlas y almacenarlas
            $variables = cache()->remember('modbus_variables_' . $this->machine, 3600, function () {
                return MachineVariable::where([
                    'machine_name' => $this->machine,
                    'is_active' => true,
                ])->get();
            });

            $data = [];
            $this->connection->connect();

            foreach ($variables as $variable) {
                try {
                    $startAddress = $variable->address - 1;
                    $packet = new ReadHoldingRegistersRequest($startAddress, $variable->words, 1);
                    $binaryData = $this->connection->sendAndReceive($packet);
                    $response = ResponseFactory::parseResponseOrThrow($binaryData);
                    $data[$variable->name] = $this->parseVariable($response, $variable);
                } catch (ModbusException $e) {
                    $data[$variable->name] = '??';
                }
            }

            return $data;
        } catch (ModbusException $e) {
            return ['error' => $e->getMessage()];
        } finally {
            $this->connection->close();
        }
    }
}
