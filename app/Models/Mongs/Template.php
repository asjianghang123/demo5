<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $connection = 'mongs';
    protected $table = 'template';
    public $timestamps = false;
}