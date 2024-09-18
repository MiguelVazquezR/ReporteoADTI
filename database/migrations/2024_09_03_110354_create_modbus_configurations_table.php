<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modbus_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('machine');
            $table->unsignedSmallInteger('sampling_minutes')->default(5);
            $table->string('host')->default('192.168.0.1'); // IP por defecto
            $table->integer('port')->default(502); // Puerto por defecto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modbus_configurations');
    }
};
