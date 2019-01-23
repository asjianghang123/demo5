<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\Databaseconn2G;
use App\User;
use App\Models\Mongs\Alarm4GServerInfo;
use App\Models\Mongs\Alarm2GServerInfo;


class ENIQControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testQuery4G
//     */
//    public function testQuery4G()
//    {
//        $mock = factory(Databaseconns::class, 2)->create()->toArray();
//
//        $expect = array();
//        foreach ($mock as $item) {
//            $item['status'] = 'false';
//            array_push($expect, $item);
//        }
//
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/ENIQManage/Query4G')->seeJson([
//            'text' => 'status,connName,cityChinese,host,port,dbName,userName,password,subNetwork,subNetworkFdd',
//            'rows' => $expect
//        ]);
//    }

//    /**
//     * testQuery2G
//     */
//    public function testQuery2G()
//    {
//        $mock = factory(Databaseconn2G::class, 2)->create()->toArray();
//
//        $expect = array();
//        foreach ($mock as $item) {
//            $item['status'] = 'false';
//            array_push($expect, $item);
//        }
//
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/ENIQManage/Query2G')->seeJson([
//            'text' => 'status,connName,cityChinese,host,port,dbName,userName,password',
//            'rows' => $expect
//        ]);
//    }

    public function testDelteENIQ()
    {
        $pm4G = factory(Databaseconns::class, 2)->create();
        $id4G = $pm4G->pluck('id')->get(0);
        $pm2G = factory(Databaseconn2G::class, 2)->create();
        $id2G = $pm2G->pluck('id')->get(0);
        $alarm = factory(Alarm4GServerInfo::class, 2)->create();
        $idAlarm = $alarm->pluck('id')->get(0);
        $user = User::create(['type' => 'admin']);

        $this->assertTrue(Databaseconns::where('id', $id4G)->exists());
        $this->actingAs($user)->json('get', '/ENIQManage/deleteENIQ', ['sign' => 1, 'id' => $id4G])
            ->assertResponseOk();
        $this->assertFalse(Databaseconns::where('id', $id4G)->exists());

        $this->assertTrue(Databaseconn2G::where('id', $id2G)->exists());
        $this->actingAs($user)->json('get', '/ENIQManage/deleteENIQ', ['sign' => 2, 'id' => $id2G])
            ->assertResponseOk();
        $this->assertFalse(Databaseconn2G::where('id', $id2G)->exists());

        $this->assertTrue(Alarm4GServerInfo::where('id', $idAlarm)->exists());
        $this->actingAs($user)->json('get', '/ENIQManage/deleteENIQ', ['sign' => 3, 'id' => $idAlarm])
            ->assertResponseOk();
        $this->assertFalse(Alarm4GServerInfo::where('id', $idAlarm)->exists());

    }

    public function testUpdateENIQ()
    {
        //2G
        $parameters = ['ENIQSign' => '2G', 'connName' => 'changzhou', 'cityChinese' => '常州', 'host' => '10.40.57.187',
            'port' => '2640', 'dbName' => 'dwhdb', 'userName' => 'dcbo', 'password' => 'dcbo'];
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/ENIQManage/updateENIQ', $parameters)->assertResponseOk();
        $this->assertTrue(Databaseconn2G::where('connName', 'changzhou')->exists());
        $parameters['connName'] = 'nantong';
        $parameters['ENIQId'] = 1;
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/ENIQManage/updateENIQ', $parameters)->assertResponseOk();
        $this->assertFalse(Databaseconn2G::where('connName', 'changzhou')->exists());
        $this->assertTrue(Databaseconn2G::where('connName', 'nantong')->exists());

        //4G
        unset($parameters['ENIQId']);
        $parameters['ENIQSign'] = '4G';
        $parameters['subNetwork'] = 'changzhouLTE';
        $parameters['subNetworkFdd'] = 'changzhouLTEFDD';
        $this->actingAs($user)->json('get', '/ENIQManage/updateENIQ', $parameters)->assertResponseOk();
        $this->assertTrue(Databaseconns::where('connName', 'nantong')->exists());
        $parameters['connName'] = 'changzhou';
        $parameters['ENIQId'] = 1;
        $this->actingAs($user)->json('get', '/ENIQManage/updateENIQ', $parameters)->assertResponseOk();
        $this->assertFalse(Databaseconns::where('connName', 'nantong')->exists());
        $this->assertTrue(Databaseconns::where('connName', 'changzhou')->exists());
    }

//    /**
//     * testQuery4GAlarm
//     */
//    public function testQuery4GAlarm()
//    {
//        $alarmServers = factory(Alarm4GServerInfo::class, 10)->create()->toArray();
//        $items = array();
//        foreach ($alarmServers as $alarmServer) {
//            $alarmServer['status'] = 'false';
//            array_push($items, $alarmServer);
//        }
//
//        $expected = array();
//        $expected['text'] = 'status,serverName,city,host,port,dbName,userName,password';
//        $expected['rows'] = $items;
//
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/ENIQManage/Query4GAlarm')->seeJson($expected);
//    }

//    /**
//     * testQuery2GAlarm
//     */
//    public function testQuery2GAlarm()
//    {
//        $alarmServers = factory(Alarm2GServerInfo::class, 10)->create()->toArray();
//        $items = array();
//        foreach ($alarmServers as $alarmServer) {
//            $alarmServer['status'] = 'false';
//            array_push($items, $alarmServer);
//        }
//
//        $expected = array();
//        $expected['text'] = 'status,serverName,city,host,port,dbName,userName,password';
//        $expected['rows'] = $items;
//
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/ENIQManage/Query2GAlarm')->seeJson($expected);
//    }

    /**
     * testUpdateAlarm
     */
    public function testUpdateAlarm()
    {
        //test delete 4G alarm
        $attributes = ['alarmServerName' => 'changzhou', 'alarmSign' => '4GAlarm', 'alarmCity' => '常州', 'alarmHost' => '10.40.57.190',
            'alarmPort' => '2640', 'alarmDbName' => 'dwhdb', 'alarmUserName' => 'dcbo', 'alarmPassword' => 'dcbo'];
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/ENIQManage/updateAlarm', $attributes)->assertResponseOk();
        $this->assertTrue(Alarm4GServerInfo::where('serverName', 'changzhou')->exists());

        $attributes['alarmServerName'] = 'nantong';
        $attributes['alarmId'] = 1;

        $this->actingAs($user)->json('post', '/ENIQManage/updateAlarm', $attributes)->assertResponseOk();
        $this->assertFalse(Alarm4GServerInfo::where('serverName', 'changzhou')->exists());
        $this->assertTrue(Alarm4GServerInfo::where('serverName', 'nantong')->exists());

        //test delete 2G alarm
        unset($attributes['alarmId']);
        $attributes['alarmSign'] = '2GAlarm';

        $this->actingAs($user)->json('post', '/ENIQManage/updateAlarm', $attributes)->assertResponseOk();
        $this->assertTrue(Alarm2GServerInfo::where('serverName', 'nantong')->exists());
        $attributes['alarmServerName'] = 'changzhou';
        $attributes['alarmId'] = 1;
        $this->actingAs($user)->json('post', '/ENIQManage/updateAlarm', $attributes)->assertResponseOk();
        $this->assertFalse(Alarm2GServerInfo::where('serverName', 'nantong')->exists());
        $this->assertTrue(Alarm2GServerInfo::where('serverName', 'changzhou')->exists());
    }
}
