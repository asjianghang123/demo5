<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class FormulaParaBaseline extends Model
{
    protected $connection = 'mongs';
    protected $table = 'formulaParaBaseline';
    public $timestamps = false;
}