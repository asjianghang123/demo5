<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class AlarmInfo extends Model
{
    protected $connection = 'mongs';
    protected $table = 'AlarmInfo';
    public $timestamps = false;
}