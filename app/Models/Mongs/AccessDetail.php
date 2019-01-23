<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class AccessDetail extends Model
{
    protected $connection = 'mongs';
    protected $table = 'access_detail';
    public $timestamps = false;
}
