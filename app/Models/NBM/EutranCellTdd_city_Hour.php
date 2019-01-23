<?php

namespace App\Models\NBM;

use Illuminate\Database\Eloquent\Model;

class EutranCellTdd_city_Hour extends Model
{
    protected $connection = 'nbm';
    protected $table = 'EutranCellTdd_city_hour';
    public $timestamps = false;
}