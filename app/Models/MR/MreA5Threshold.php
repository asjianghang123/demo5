<?php

namespace App\Models\MR;

use Illuminate\Database\Eloquent\Model;

class MreA5Threshold extends Model
{
    protected $connection = 'MR_CZ';
    protected $table = 'mreA5Threshold';
    public $timestamps = false;
    public $incrementing = false;
}
