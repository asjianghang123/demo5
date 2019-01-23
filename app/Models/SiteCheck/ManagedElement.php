<?php

namespace App\Models\SiteCheck;

use Illuminate\Database\Eloquent\Model;

class ManagedElement extends Model
{
    protected $connection = 'kget';
    protected $table = 'ManagedElement';
    public $timestamps = false;
}
