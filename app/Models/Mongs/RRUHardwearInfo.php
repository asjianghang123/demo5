<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class RRUHardwearInfo extends Model
{
	protected $connection = 'mongs';
    protected $table = 'ru_hardware_info';
    public $timestamps = false;
}
