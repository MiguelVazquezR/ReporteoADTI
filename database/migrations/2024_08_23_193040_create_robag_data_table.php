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
        Schema::create('robag_data', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('scale_low_product')->nullable();
            $table->string('reset_counters')->nullable();
            $table->string('scale_internal_status')->nullable();
            $table->string('robag_up_time')->nullable();
            $table->string('reason_for_stop_status')->nullable();
            $table->string('run_time')->nullable();
            $table->string('paused_time')->nullable();
            $table->string('fault_time')->nullable();
            $table->string('out_on_film_time')->nullable();
            $table->string('last_metal_detect_time')->nullable();
            $table->string('bags_this_roll')->nullable();
            $table->string('bags_last_roll')->nullable();
            $table->string('PISD_reject_count')->nullable();
            $table->string('scale_good_bags')->nullable();
            $table->string('scale_overweight_bags')->nullable();
            $table->string('scale_underweight_bags')->nullable();
            $table->string('scale_overscale_count')->nullable();
            $table->string('scale_warnings')->nullable();
            $table->string('full_bags')->nullable();
            $table->string('empty_bags')->nullable();
            $table->string('efficiency_percentage')->nullable();
            $table->string('target_weight')->nullable();
            $table->string('total_dump_weight')->nullable();
            $table->string('bags_per_minute')->nullable();
            $table->string('total_giveaway')->nullable();
            $table->string('giveaway_percentage')->nullable();
            $table->string('total_waste')->nullable();
            $table->string('total_bags')->nullable();
            $table->string('mean_weight')->nullable();
            $table->string('standard_deviation')->nullable();
            $table->string('total_dumps')->nullable();
            $table->string('gas_total')->nullable();
            $table->string('scale_bpm')->nullable();
            $table->string('heads_per_dump')->nullable();
            $table->string('average_weight_in_ready_heads')->nullable();
            $table->string('short_term_scale_efficiency_percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('robag_data');
    }
};
