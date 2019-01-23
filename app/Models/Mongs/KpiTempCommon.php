<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class KpiTempCommon extends Model
{
    protected $connection = 'mongs';
    protected $table = 'kpiTemplateCommon';
    public $timestamps = false;
}