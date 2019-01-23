<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TABLES extends Model
{
    protected $connection = 'information_schema';
    protected $table = 'information_schema.TABLES';
    public $timestamps = false;
}