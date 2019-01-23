<?php

namespace App\Models\CDR;

use Illuminate\Database\Eloquent\Model;

class CauseCode extends Model
{
    protected $connection = 'CDR';
    protected $table = 'causeCode';
    public $timestamps = false;
}