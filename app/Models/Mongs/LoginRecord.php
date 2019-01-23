<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class LoginRecord extends Model
{
    protected $connection = 'mongs';
    protected $table = 'login_record';
    public $timestamps = false;
}