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
            return $variable->type == 'uint'
                ? $response->getWordAt($address)->getUInt16()
                : $response->getWordAt($address)->getInt16();
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
            $variables = MachineVariable::where('machine_name', $this->machine)->get();
            $data = [];

            $this->connection->connect();

            foreach ($variables as $variable) {
                $startAddress = $variable->variable_address - 1;
                $packet = new ReadHoldingRegistersRequest($startAddress, $variable->words, 1);
                $binaryData = $this->connection->sendAndReceive($packet);
                $response = ResponseFactory::parseResponseOrThrow($binaryData);

                $data[$variable->variable_original_name] = $this->parseVariable($response, $variable);
            }

            return $data;
        } catch (ModbusException $e) {
            return ['error' => $e->getMessage()];
        } finally {
            $this->connection->close();
        }
    }
}
