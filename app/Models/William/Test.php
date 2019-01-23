<?php

namespace App\Models\William;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $connection = 'William';
    protected $table = 'test';
    public $timestamps = false;
}
