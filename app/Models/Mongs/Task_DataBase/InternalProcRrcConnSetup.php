<?php

namespace App\Models\Mongs\Task_DataBase;

use Illuminate\Database\Eloquent\Model;

class InternalProcRrcConnSetup extends Model
{
    protected $connection = 'kget';
    protected $table = 'internalProcRrcConnSetup';
    public $timestamps = false;
}
