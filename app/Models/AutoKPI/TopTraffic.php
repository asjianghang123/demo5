<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class TopTraffic extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'TopTraffic';
    public $timestamps = false;
}
