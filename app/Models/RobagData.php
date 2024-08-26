<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RobagData extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'scale_low_product',
        'reset_counters',
        'scale_internal_status',
        'robag_up_time',
        'reason_for_stop_status',
        'run_time',
        'paused_time',
        'fault_time',
        'out_on_film_time',
        'last_metal_detect_time',
        'bags_this_roll',
        'bags_last_roll',
        'PISD_reject_count',
        'scale_good_bags',
        'scale_overweight_bags',
        'scale_underweight_bags',
        'scale_overscale_count',
        'scale_warnings',
        'full_bags',
        'empty_bags',
        'efficiency_percentage',
        'target_weight',
        'total_dump_weight',
        'bags_per_minute',
        'total_giveaway',
        'giveaway_percentage',
        'total_waste',
        'total_bags',
        'mean_weight',
        'standard_deviation',
        'total_dumps',
        'gas_total',
        'scale_bpm',
        'heads_per_dump',
        'average_weight_in_ready_heads',
        'short_term_scale_efficiency_percentage',
    ];
}
