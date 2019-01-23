<?php

namespace App\Models\Kget;

use Illuminate\Database\Eloquent\Model;

class LicenseAnalysis extends Model
{
    protected $connection = 'kget';
    protected $table = 'LicenseAnalysis';
    public $timestamps = false;
}