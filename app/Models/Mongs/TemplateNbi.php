<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class TemplateNbi extends Model
{
    protected $connection = 'mongs';
    protected $table = 'templateNbi';
    public $timestamps = false;
}