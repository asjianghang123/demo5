<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Databaseconn2G extends Model
{
    protected $connection = 'mongs';
    protected $table = 'databaseconn_2G';
    public $timestamps = false;
}