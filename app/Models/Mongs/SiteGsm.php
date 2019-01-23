<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class SiteGsm extends Model
{
    protected $connection = 'mongs';
    protected $table = 'siteGsm';
    public $timestamps = false;
}