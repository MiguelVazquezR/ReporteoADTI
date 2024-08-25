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
        Schema::create('machine_variables', function (Blueprint $table) {
            $table->id();
            $table->string('machine_name');
            $table->string('variable_name');
            $table->text('variable_description')->nullable();
            $table->text('variable_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_variables');
    }
};
