<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $connection = 'mongs';
    protected $table = 'users_group';
    public $timestamps = false;

}
