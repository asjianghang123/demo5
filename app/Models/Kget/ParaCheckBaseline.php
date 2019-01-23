<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class ParaCheckBaseline extends Model
{
    protected $connection = 'kget';
    protected $table = 'ParaCheckBaseline';
    public $timestamps = false;
}