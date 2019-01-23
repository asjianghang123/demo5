<?php

namespace App\Models\MR;

use Illuminate\Database\Eloquent\Model;

class MroPciMod3_day extends Model
{
    protected $connection = 'MR_CZ';
    protected $table = 'mroPciMod3_day';
    public $timestamps = false;
}
