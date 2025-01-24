<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Robag1 extends Model
{
    use HasFactory;

    protected $table = 'robag1';

    protected $fillable = [
        'average_weigth',
        'bags_per_minute',
        'empty_bags',
        'fault_time',
        'full_bags',
        'interlock_time',
        'mean_weight',
        'out_of_film_time',
        'paused_time',
        'robag_up_time',
        'run_time',
        'scale_good_bags',
        'standard_deviation',
        'total_bags',
        'total_waste',
        'total_dump_weight',
    ];
}
