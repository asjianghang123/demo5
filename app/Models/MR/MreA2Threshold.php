<?php

namespace App\Models\MR;

use Illuminate\Database\Eloquent\Model;

class MreA2Threshold extends Model
{
    protected $connection = 'MR_CZ';
    protected $table = 'mreA2Threshold';
    public $timestamps = false;
    public $incrementing = false;
}
