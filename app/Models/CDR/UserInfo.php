<?php

namespace App\Models\CDR;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $connection = 'CDR';
    protected $table = 'userInfo';
    public $timestamps = false;
    protected $visible = ['imsi','msisdn','imeiTac','imei','TAC','MarketingName','ManufacturerOrApplicant','Band','RadioInterface','BrandName','ModelName','OperatingSystem','NFC','Bluetooth','WLAN','DeviceType'];
}
