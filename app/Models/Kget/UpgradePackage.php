<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class UpgradePackage extends Model
{
    protected $connection = 'kget';
    protected $table = 'UpgradePackage';
    public $timestamps = false;
}
