<?php

namespace App\Models\NBI;

use Illuminate\Database\Eloquent\Model;

class EutranCellTdd_city_Quarter extends Model
{
    protected $connection = 'nbi';
    protected $table = 'EutranCellTdd_city_Quarter';
    public $timestamps = false;
}