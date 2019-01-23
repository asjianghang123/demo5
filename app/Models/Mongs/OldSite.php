<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class OldSite extends Model
{
	protected $connection = 'mongs';
    protected $table = 'OldSiteOpenRemind';
    public $timestamps = false;
}
