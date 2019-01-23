<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class NeighOptimizationWhiteList extends Model
{
    public $timestamps = false;
    protected $connection = 'mongs';
    protected $table = 'NeighOptimizationWhiteList';
    protected $fillable = ['OptimizationType', 'dataType', 'city', 'ecgi'];
}
