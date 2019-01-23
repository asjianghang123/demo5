<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class LowAccessCell extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'lowAccessCell';
    public $timestamps = false;
}