<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class InterfereCell extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'interfereCell';
    public $timestamps = false;
}