<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\CDR\Irat4to2;
use App\Models\Mongs\NeighOptimizationWhiteList;
use App\User;

class CDRNeighAnalysisControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testGetHeader
//     */
//    public function testGetHeader()
//    {
//        $expect = (new Irat4to2)->getVisible();
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/GSMNeighborAnalysis/getCdrServeNeighDataHeader')
//            ->seeJson(['field' => implode(',', $expect)]);
//    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        $mocks = factory(Irat4to2::class, 10)->create()->sortByDesc('users');
        $ecgi = $mocks->first()->ecgi;
        $date_id = $mocks->first()->date_id;

        //create white list
        NeighOptimizationWhiteList::create([
            'OptimizationType' => '补2G邻区分析',
            'dataType' => 'MRE数据',
            'city' => '常州',
            'ecgi' => $ecgi,
        ]);

        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->ecgi != $ecgi && $mock->date_id == $date_id) {
                array_push($expect, $mock->toArray());
            }
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/GSMNeighborAnalysis/getCdrServeNeighData', ['OptimizationType' => '补2G邻区分析',
            'dataType' => 'MRE数据', 'dataBase' => '常州', 'dateTime' => $date_id])->seeJson(
            ['total' => count($expect), 'records' => $expect]);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        $mocks = factory(Irat4to2::class, 2)->create()->sortByDesc('users');
        $ecgi = $mocks->first()->ecgi;
        $date_id = $mocks->first()->date_id;

        //create white list
        NeighOptimizationWhiteList::create([
            'OptimizationType' => '补2G邻区分析',
            'dataType' => 'MRE数据',
            'city' => '常州',
            'ecgi' => $ecgi,
        ]);

        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('get', '/GSMNeighborAnalysis/getAllCdrServeNeighData', ['OptimizationType' => '补2G邻区分析', 'dataType' => 'MRE数据', 'dataBase' => '常州', 'dateTime' => $date_id])->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->ecgi != $ecgi && $mock->date_id == $date_id) {
                $mock = collect($mock->toArray())->forget(['Longitude_4g','Latitude_4g','Longitude_2g','Latitude_2g']);
                array_push($expect, array_values($mock->toArray()));
            }
        }

        $reader = \League\Csv\Reader::createFromPath($fileName);
        $content = $reader->setOffset(1)->fetchAll();
        $this->assertEquals($expect, $content);
    }
}
