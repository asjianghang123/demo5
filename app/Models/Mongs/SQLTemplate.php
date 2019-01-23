<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class SQLTemplate extends Model
{
    protected $connection = 'mongs';
    protected $table = 'SQLTemplate';
    public $timestamps = false;
}