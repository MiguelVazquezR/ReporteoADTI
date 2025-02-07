<?php

namespace Database\Seeders;

use App\Models\Machine;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Machine::create([
            'name' => 'Robag 1',
            'class_name' => 'Robag1',
            'in_view' => true,
        ]);

        $this->call([
            MachineVariableSeeder::class,
        ]);
    }
}
