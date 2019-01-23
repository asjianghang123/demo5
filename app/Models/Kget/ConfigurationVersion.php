<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class ConfigurationVersion extends Model
{
    protected $connection = 'kget';
    protected $table = 'ConfigurationVersion';
    public $timestamps = false;
}
