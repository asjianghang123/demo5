<?php

namespace App\Models\Alarm;

use Illuminate\Database\Eloquent\Model;

class AlarmInfo extends Model
{
    protected $connection = 'alarm';
    protected $table = 'AlarmInfo';
    public $timestamps = false;
}