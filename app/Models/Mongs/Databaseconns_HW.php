<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Databaseconns_HW extends Model
{
    public $timestamps = false;
    protected $connection = 'mongs';
    protected $table = 'databaseconn_HW';
    protected $fillable = ['connName', 'cityChinese'];
}
