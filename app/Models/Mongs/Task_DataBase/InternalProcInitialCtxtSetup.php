<?php

namespace App\Models\Mongs\Task_DataBase;

use Illuminate\Database\Eloquent\Model;

class InternalProcInitialCtxtSetup extends Model
{
    protected $connection = 'kget';
    protected $table = 'internalProcInitialCtxtSetup';
    public $timestamps = false;
}
