<?php

namespace App\Models\Alarm;

use Illuminate\Database\Eloquent\Model;

class FMA_alarm_log extends Model
{
    protected $connection = 'alarm';
    protected $table = 'FMA_alarm_log';
    public $timestamps = false;
}