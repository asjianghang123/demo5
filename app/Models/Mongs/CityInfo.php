<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class CityInfo extends Model
{
    protected $connection = 'mongs';
    protected $table = 'city_info';
    public $timestamps = false;
}