<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class LowAccessCell_ex extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'lowAccessCell_ex';
    public $timestamps = false;
}