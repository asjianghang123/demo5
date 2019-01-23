<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class TempBSCCA extends Model
{
    protected $connection = 'kget';
    protected $table = 'tempBSCCA';
    public $timestamps = false;
}