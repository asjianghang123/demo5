<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class RruSlotSerialNumberRecord extends Model
{
    protected $connection = 'mongs';
    protected $table = 'rruSlotSerialNumberRecord';
    public $timestamps = false;
}