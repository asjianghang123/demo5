<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Databaseconns extends Model
{
    public $timestamps = false;
    protected $connection = 'mongs';
    protected $table = 'databaseconn';
    protected $fillable = ['connName', 'cityChinese'];
}
