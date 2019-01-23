<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class MailList extends Model
{
    public $timestamps = false;
    protected $connection = 'mongs';
    protected $table = 'mail_list';
    protected $fillable = ['mailAddress', 'name', 'role', 'scope', 'city'];
}