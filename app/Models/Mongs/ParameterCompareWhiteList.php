<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class ParameterCompareWhiteList extends Model
{
    protected $connection = 'mongs';
    protected $table = 'parameterCompareWhiteList';
    public $timestamps = false;
}