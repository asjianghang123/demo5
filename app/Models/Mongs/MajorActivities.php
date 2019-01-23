<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class MajorActivities extends Model
{
    protected $connection = 'mongs';
    protected $table = 'majorActivities';
    public $timestamps = false;
}