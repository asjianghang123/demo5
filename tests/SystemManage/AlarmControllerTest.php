<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\AlarmInfo;
use App\User;

class AlarmControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testGetAccessData
     *
     * @return void
     */
    public function testGetAlarm()
    {
        $mock = factory(AlarmInfo::class, 5)->create();
        $expected = $mock->flatten()->toArray();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/alarmManage/getAlarm')
            ->seeJson(['rows' => $expected]);
    }
}
