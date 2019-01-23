<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'mongs';
    protected $table = 'task';
    public $timestamps = false;
}