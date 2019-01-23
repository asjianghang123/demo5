<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class MailGroup extends Model
{
    public $timestamps = false;
    protected $connection = 'mongs';
    protected $table = 'mail_group';
    protected $fillable = ['scopeName', 'scope', 'roleName', 'role'];
}