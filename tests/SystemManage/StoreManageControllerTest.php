<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\Databaseconns;
use App\User;
use App\Models\Mongs\TraceServerInfo;

class StoreManageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testTreeQuery
     */
    public function testTreeQuery()
    {
        factory(Databaseconns::class, 10)->create();
        $expected = array();
        array_push($expected, array("id" => 0, "text" => "全部城市", "value" => "city"));
        $items = Databaseconns::query()->groupBy('cityChinese')->get();
        foreach ($items as $item) {
            array_push($expected, ['id' => $item['id'], 'text' => $item['cityChinese'], 'value' => $item['connName']]);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/storeManage/treeQuery')->seeJson($expected);
    }

    /**
     * testGetTableData
     */
    public function testGetTableData()
    {
        $mock = factory(TraceServerInfo::class, 10)->create();
        $city = $mock->first()->city;

        $expect = $mock->where('city', $city)->flatten()->toArray();

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/storeManage/getTableData', ['city' => $city, 'limit' => 10])
            ->seeJson(['records' => $expect]);
    }

    /**
     * testUpdateDownload
     */
    public function testUpdateDownload()
    {
        $mock = factory(TraceServerInfo::class)->create();
        $id = $mock->id;
        //test update
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/storeManage/updateDownload', ['downloadId' => $id, 'serverName' => 'test',
            'citys' => 'test', 'type' => 'test', 'ipAddress' => 'test', 'sshUserName' => 'test', 'sshPassword' => 'test',
            'ftpUserName' => 'test', 'ftpPassword' => 'test', 'fileDir' => 'test']);
        $exists = TraceServerInfo::where('id', $id)->where('ipAddress', 'test')->exists();
        $this->assertTrue($exists);

        //test create
        $this->actingAs($user)->json('post', '/storeManage/updateDownload', ['serverName' => 'test',
            'citys' => 'test', 'type' => 'test', 'ipAddress' => 'test2', 'sshUserName' => 'test', 'sshPassword' => 'test',
            'ftpUserName' => 'test', 'ftpPassword' => 'test', 'fileDir' => 'test']);
        $this->assertTrue(TraceServerInfo::query()->getQuery()->where('ipAddress', 'test2')->exists());
    }

    /**
     * testDeleteDownload
     */
    public function testDeleteDownload()
    {
        $mock = factory(TraceServerInfo::class)->create();
        $id = $mock->id;

        $user = User::create(['type' => 'admin']);
        $this->assertTrue(TraceServerInfo::where('id', $id)->exists());
        $this->actingAs($user)->json('get', '/storeManage/deleteDownload', ['id' => $id]);
        $this->assertFalse(TraceServerInfo::where('id', $id)->exists());
    }

    /**
     * testGetTypes
     */
    public function testGetTypes()
    {
        $mock = factory(TraceServerInfo::class, 10)->create();
        $types = $mock->groupBy('type')->keys()->toArray();
        $expect = array();
        foreach ($types as $type) {
            array_push($expect, ['label' => $type, 'value' => $type]);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/storeManage/getTypes')->seeJson($expect);
    }
}
