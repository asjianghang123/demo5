<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class SiteLte extends Model
{
	protected $connection = 'mongs';
    protected $table = 'siteLte';
    public $timestamps = false;
}
