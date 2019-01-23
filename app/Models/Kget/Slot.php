<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $connection = 'kget';
    protected $table = 'Slot';
    public $timestamps = false;
}
