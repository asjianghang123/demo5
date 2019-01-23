<?php

namespace App\Models\Mongs;

use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    protected $connection = 'kget';
    protected $table = 'eventDetail';
    public $timestamps = false;
    protected $visible = ['id','eventName','eventTime','hourId','imsi','mTmsi','ueRef','enbS1apId','mmeS1apId','ecgi','gummei','direction','contents'];
}