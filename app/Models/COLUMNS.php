<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class COLUMNS extends Model
{
    protected $connection = 'information_schema';
    protected $table = 'information_schema.COLUMNS';
    public $timestamps = false;
}