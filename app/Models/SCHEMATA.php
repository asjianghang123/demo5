<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SCHEMATA extends Model
{
    protected $connection = 'information_schema';
    protected $table = 'SCHEMATA';
    public $timestamps = false;
}