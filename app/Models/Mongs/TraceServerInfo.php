<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class TraceServerInfo extends Model
{
    protected $connection = 'mongs';
    protected $table = 'traceServerInfo';
    public $timestamps = false;
}