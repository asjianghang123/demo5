<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class AlarmServerInfo extends Model
{
    protected $connection = 'mongs';
    protected $table = 'alarmServerInfo';
    public $timestamps = false;
}