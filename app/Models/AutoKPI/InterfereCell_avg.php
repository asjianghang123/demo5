<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class InterfereCell_avg extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'interfereCell_avg';
    public $timestamps = false;
}