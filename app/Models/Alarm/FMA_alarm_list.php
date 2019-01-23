<?php

namespace App\Models\Alarm;

use Illuminate\Database\Eloquent\Model;

class FMA_alarm_list extends Model
{
    protected $connection = 'alarm';
    protected $table = 'FMA_alarm_list';
    public $timestamps = false;

    public function siteLte()
    {
        return $this->hasOne('App\Models\Mongs\SiteLte','siteName','meContext');
    }
    public function alarmInfo()
    {
        return $this->hasOne('App\Models\Alarm\AlarmInfo','alarmNameE','SP_text');
    }
}