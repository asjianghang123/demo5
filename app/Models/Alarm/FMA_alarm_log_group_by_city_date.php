<?php

namespace App\Models\Alarm;

use Illuminate\Database\Eloquent\Model;

class FMA_alarm_log_group_by_city_date extends Model
{
    protected $connection = 'alarm';
    protected $table = 'FMA_alarm_log_group_by_city_date';
    public $timestamps = false;
}