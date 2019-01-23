<?php

namespace App\Models\SiteCheck;

use Illuminate\Database\Eloquent\Model;

class SiteStatus extends Model
{
    protected $connection = 'new_site_check';
    protected $table = 'site_status';
    public $timestamps = false;
}
