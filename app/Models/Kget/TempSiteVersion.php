<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class TempSiteVersion extends Model
{
    protected $connection = 'kget';
    protected $table = 'TempSiteVersion';
    public $timestamps = false;
}
