<?php

namespace Database\Seeders;

use App\Models\ModbusConfiguration;
use App\Models\RobagData;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // ejecutar seeders
        
        $this->call([
            MachineVariableSeeder::class,
        ]);
        
        RobagData::factory()->count(300)->create();
        
        ModbusConfiguration::create([
            'host' => '127.0.0.1',
            'port' => 502,
            'machine' => 'Robag1',
            'sampling_minutes' => 5,
        ]);
    }
    
}
