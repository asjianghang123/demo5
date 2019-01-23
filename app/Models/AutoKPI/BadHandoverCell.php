<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class BadHandoverCell extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'badHandoverCell';
    public $timestamps = false;
}