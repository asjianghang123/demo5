<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Mongs\LoginRecord;

class LoginSuccessListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        //验证成功后，登录之前，在login_record表中记录一下
        $loginRecord = new LoginRecord();
        $loginRecord->user = $event->user->user;
        date_default_timezone_set("PRC");
        $loginRecord->login_time = date("Y-m-d H:i:s");
        $loginRecord->save();
    }
}
