<?php

namespace App\Models\CTR;

use Illuminate\Database\Eloquent\Model;

class InternalProcRrcConnSetup extends Model
{
    protected $connection = 'CTR';
    protected $table = 'internalProcRrcConnSetup';
    public $timestamps = false;
}
