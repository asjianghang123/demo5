<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class MOsMeContexts extends Model
{
    protected $connection = 'mongs';
    protected $table = 'MOsMeContexts';
    public $timestamps = false;
}