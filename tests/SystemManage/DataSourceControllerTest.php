<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\TraceServerInfo;
use App\Models\Mongs\Databaseconns;
use App\User;

class DataSourceControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testGetLogType
     */
    public function testGetLogType()
    {
        $mock = factory(TraceServerInfo::class, 10)->create();
        $types = $mock->groupBy('type')->keys()->toArray();

        $expect = array();

        foreach ($types as $type) {
            if ($type != 'ctr') {
                continue;
            }
            array_push($expect, ['value' => $type, 'text' => strtoupper($type)]);
        }

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/dataSourceManage/getLogType')->seeJson($expect);
    }

    /**
     * testGetNode
     */
    public function testGetNode()
    {
        $mock = factory(TraceServerInfo::class, 10)->create();
        $type = $mock->first()->type;


        $expect = array();
        $servers = $mock->where('type', $type);
        foreach ($servers as $server) {
            Databaseconns::create(['connName' => $server->city, 'cityChinese' => $server->city]);
        }

        foreach ($servers as $server) {
            $item = array();
            $item['value'] = $server->ipAddress;
            $item['text'] = $server->city;
            $item['sshUserName'] = $server->sshUserName;
            $item['sshPassword'] = $server->sshPassword;
            $item['fileDir'] = $server->fileDir;
            array_push($expect, $item);
        }

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/dataSourceManage/getNode', ['logType' => $type])->seeJson($expect);
    }
}
