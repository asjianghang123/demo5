<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class Latloncheck extends Model
{
    protected $connection = 'kget';
    protected $table = 'latloncheck';
    public $timestamps = false;
}