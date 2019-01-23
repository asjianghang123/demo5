<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongs';
    protected $table = 'notification';
    public $timestamps = false;
}
