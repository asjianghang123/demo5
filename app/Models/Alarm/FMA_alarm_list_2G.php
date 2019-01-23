<?php

namespace App\Models\Alarm;

use Illuminate\Database\Eloquent\Model;

class FMA_alarm_list_2G extends Model
{
    protected $connection = 'alarm_2G';
    protected $table = 'FMA_alarm_list';
    public $timestamps = false;
}