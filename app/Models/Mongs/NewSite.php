<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class NewSite extends Model
{
	protected $connection = 'mongs';
    protected $table = 'NewSiteRemind';
    public $timestamps = false;
}
