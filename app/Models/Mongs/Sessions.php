<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $connection = 'mongs';
    protected $table = 'sessions';
    public $timestamps = false;
}
