<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class FtpServerInfo extends Model
{
    protected $connection = 'mongs';
    protected $table = 'ftpServerInfo';
    public $timestamps = false;
}