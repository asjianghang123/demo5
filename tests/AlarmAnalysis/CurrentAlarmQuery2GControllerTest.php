<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrentAlarmQuery2GControllerTest extends TestCase
{
//    use WithoutMiddleware;

    public function testGetUser()
    {
        $this->assertTrue(true);
        return \App\User::query()->where('user', 'admin')->first();
    }

    /**
     * @depends testGetUser
     */
    public function testViews()
    {
        $response = $this->actingAs(func_get_args()[0])
            ->call('GET', '/currentAlarmQuery2G');
        $this->assertEquals('200', $response->status());
    }

    /**
     * @depends testGetUser
     */
    public function testGetCitys()
    {
        $this->actingAs(func_get_args()[0])
            ->json('GET','/currentAlarmQuery2G/getCitys')
            ->seeJson([]);
    }

    /**
     * @depends testGetUser
     */
    public function testGetTableData()
    {
        $this->actingAs(func_get_args()[0])
            ->json('GET', '/currentAlarmQuery2G/getTableData',
                ['placeDim'=>'ManagedElement', 'placeDimName'=>'', 'limit'=>'50'])
            ->seeJson([]);
    }

    /**
     * @depends testGetUser
     */
    public function testGetAllTableData()
    {
        $this->markTestSkipped()
            ->actingAs(func_get_args()[0])
            ->json('POST', '/currentAlarmQuery2G/getAllTableData',
                ['placeDim'=>'ManagedElement', 'placeDimName'=>'', 'city'=>'changzhou'])
            ->seeJson([]);
    }

    public function testUploadFile()
    {

    }


}
