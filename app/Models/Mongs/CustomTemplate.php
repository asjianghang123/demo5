<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class CustomTemplate extends Model
{
    protected $connection = 'mongs';
    protected $table = 'customTemplate';
    public $timestamps = false;
}
