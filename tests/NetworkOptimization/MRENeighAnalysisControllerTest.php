<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\NeighOptimizationWhiteList;
use App\Models\MR\MreServeNeigh_day;
use App\User;

class MRENeighAnalysisControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testGetHeader
//     */
//    public function testGetHeader()
//    {
//        $expect = (new MreServeNeigh_day)->getVisible();
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/MROServeNeighAnalysis/getMreServeNeighDataHeader')
//            ->seeJson(['field' => implode(',', $expect)]);
//    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        //create mock data
        $mocks = factory(MreServeNeigh_day::class, 2)->create();
        $dateTime = $mocks->first()->dateId;
        $ecgi = $mocks->first()->ecgi;

        //create white list.
        NeighOptimizationWhiteList::create(['ecgi' => $ecgi, 'OptimizationType' => 'test', 'dataType' => 'test',
            'city' => '常州']);

        //create expect data
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->mr_LteScEarfcn != $mock->mr_LteNcEarfcn) {
                continue;
            } else if ($mock->mr_LteNcEarfcn < 1) {
                continue;
            } else if ($mock->nc_times_ratio < 1) {
                continue;
            } else if ($mock->nc_times_ratio < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRQ < 1) {
                continue;
            } else if ($mock->avg_mr_LteNcRSRP < 1) {
                continue;
            } else if ($mock->dateId != $dateTime) {
                continue;
            } else if ($mock->ecgi == $ecgi) {
                continue;
            }
            array_push($expect, $mock->toArray());
        }
        //test
        $parameter = ['dataBase' => '常州', 'dateTime' => $dateTime, 'input1' => 1, 'input3' => 1, 'input6' => 1,
            'input7' => 1, 'input8' => 1, 'OptimizationType' => 'test', 'dataType' => 'test'];
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/MROServeNeighAnalysis/getMreServeNeighData', $parameter)
            ->seeJson(['records' => $expect]);
    }

    /**
     * testGetExportData
     */
    public function testExportData()
    {
        //create mock data.
        $mocks = factory(MreServeNeigh_day::class, 2)->create();
        $dateTime = $mocks->first()->dateId;

        //test
        $parameter = ['dataBase' => '常州', 'dateTime' => $dateTime, 'input1' => 1, 'input3' => 1, 'input6' => 1,
            'input7' => 1, 'input8' => 1];
        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('get', '/MROServeNeighAnalysis/getAllMreServeNeighData', $parameter)
            ->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        //check content.
        $reader = \League\Csv\Reader::createFromPath($fileName);
        $content = $reader->setOffset(1)->fetchAll();
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->mr_LteScEarfcn != $mock->mr_LteNcEarfcn) {
                continue;
            } else if ($mock->mr_LteNcEarfcn < 1) {
                continue;
            } else if ($mock->nc_times_ratio < 1) {
                continue;
            } else if ($mock->nc_times_ratio < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRQ < 1) {
                continue;
            } else if ($mock->avg_mr_LteNcRSRP < 1) {
                continue;
            } else if ($mock->dateId != $dateTime) {
                continue;
            }
            $mock = collect($mock->toArray())->forget(['id', 'longitude', 'latitude', 'mr_LteScEarfcn', 'mr_LteNcEarfcn', 'mr_LteNcPci', 'eventType']);
            array_push($expect, array_values($mock->toArray()));
        }
        $this->assertEquals($expect, $content);
    }
}
