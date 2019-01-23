<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class AbnormalStationCounts extends Model
{
    protected $connection = 'mongs';
    protected $table = 'abnormalStationCounts';
    public $timestamps = false;
}