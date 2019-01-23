<?php

namespace App\Models\CDR;

use Illuminate\Database\Eloquent\Model;

class L_SERVICE_REQUEST extends Model
{
    protected $connection = 'CDR';
    protected $table = 'L_SERVICE_REQUEST';
    public $timestamps = false;
}
