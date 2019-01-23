<?php

namespace App\Models\Bulkcm;

use Illuminate\Database\Eloquent\Model;

class TempParameterBulkcmCompare extends Model
{
    protected $connection = 'bulkcm';
    protected $table = 'TempParameterBulkcmCompare';
    public $timestamps = false;
}