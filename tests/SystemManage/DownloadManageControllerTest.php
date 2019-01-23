<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Models\Mongs\FtpServerInfo;

class DownloadManageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testTreeQuery
     */
    public function testTreeQuery()
    {
        $mock = factory(FtpServerInfo::class, 10)->create();
        $types = $mock->groupBy('type')->keys()->all();

        $expect = array('id' => 0, 'text' => '全部log', 'value' => 'type');
        $index = 1;
        foreach ($types as $type) {
            array_push($types, ['id' => $index++, 'text' => $type, 'value' => $type]);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/downloadManage/treeQuery')->seeJson($expect);
    }

//    /**
//     * testGetTableData
//     */
//    public function testGetTableData()
//    {
//        $servers = factory(FtpServerInfo::class, 2)->create();
//        $type = $servers->first()->type;
//
//        $expect = array();
//        foreach ($servers->where('type', $type) as $server) {
//            $item = $server->toArray();
//            $item['status'] = 'false';
//            array_push($expect, $item);
//        }
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/downloadManage/getTableData', ['type' => $type])->seeJson($expect);
//    }

    /**
     * testUpdateDownload
     */
    public function testUpdateDownload()
    {
        $user = User::create(['type' => 'admin']);
        $mock = factory(FtpServerInfo::class)->create();
        $serverName = $mock->serverName . 'update';
        //test update
        $parameter = array('downloadId' => 1, 'serverName' => $serverName);
        $this->assertFalse(FtpServerInfo::where('serverName', $serverName)->exists());
        $this->actingAs($user)->json('post', '/downloadManage/updateDownload', $parameter)->assertResponseOk();
        $this->assertTrue(FtpServerInfo::where('serverName', $serverName)->exists());

        //test insert
        $serverName = $mock->serverName . 'insert';
        $parameter['serverName'] = $serverName;
        $this->assertFalse(FtpServerInfo::where('serverName', $serverName)->exists());
        $this->actingAs($user)->json('post', '/downloadManage/updateDownload', $parameter)->assertResponseOk();
        $this->assertTrue(FtpServerInfo::where('serverName', $serverName)->exists());
    }

    /**
     * testDeleteDownload
     */
    public function testDeleteDownload()
    {
        $user = User::create(['type' => 'admin']);
        $server = factory(FtpServerInfo::class)->create();
        $id = $server->id;
        $this->assertTrue(FtpServerInfo::where('id', $id)->exists());
        $this->actingAs($user)->json('get', '/downloadManage/deleteDownload', ['id' => $id])->assertResponseOk();
        $this->assertFalse(FtpServerInfo::where('id', $id)->exists());
    }
}
