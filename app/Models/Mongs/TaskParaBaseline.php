<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class TaskParaBaseline extends Model
{
    protected $connection = 'mongs';
    protected $table = 'taskParaBaseline';
    public $timestamps = false;
}