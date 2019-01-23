<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class TrailQuery extends Model
{
    protected $connection = 'mongs';
    protected $table = 'trailQuery';
    public $timestamps = false;
}