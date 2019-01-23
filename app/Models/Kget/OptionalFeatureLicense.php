<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class OptionalFeatureLicense extends Model
{
    protected $connection = 'kget';
    protected $table = 'OptionalFeatureLicense';
    public $timestamps = false;
}