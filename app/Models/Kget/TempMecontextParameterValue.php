<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class TempMecontextParameterValue extends Model
{
    protected $connection = 'kget';
    protected $table = 'TempMecontextParameterValue';
    public $timestamps = false;
}