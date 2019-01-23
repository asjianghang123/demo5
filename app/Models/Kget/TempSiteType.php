<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class TempSiteType extends Model
{
    protected $connection = 'kget';
    protected $table = 'TempSiteType';
    public $timestamps = false;
}
