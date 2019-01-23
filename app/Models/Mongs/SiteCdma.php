<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class SiteCdma extends Model
{
    protected $connection = 'mongs';
    protected $table = 'siteCdma';
    public $timestamps = false;
}