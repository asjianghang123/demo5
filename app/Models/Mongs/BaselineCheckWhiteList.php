<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class BaselineCheckWhiteList extends Model
{
    protected $connection = 'mongs';
    protected $table = 'baselineCheckWhiteList';
    public $timestamps = false;
}