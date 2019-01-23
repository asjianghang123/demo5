<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class MenuList extends Model
{
    protected $connection = 'mongs';
    protected $table = 'menu_list';
    public $timestamps = false;
}
