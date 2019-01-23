<?php

namespace App\Models\CDR;

use Illuminate\Database\Eloquent\Model;

class Irat4to2 extends Model
{
    protected $connection = 'CDR_CZ';
    protected $table = 'irat4to2';
    public $timestamps = false;
    public $incrementing = false;
}
