<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $connection = 'mongs';
    protected $table = 'users';
    public $timestamps = false;
}