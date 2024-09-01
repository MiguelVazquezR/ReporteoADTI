<?php

namespace Database\Seeders;

use App\Models\RobagData;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\RobagDataFactory;
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

        RobagData::factory()->count(500)->create();

    }
    
}
