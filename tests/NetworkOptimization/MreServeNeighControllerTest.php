<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\MR\MreServeNeigh_day;
use App\Models\Mongs\NeighOptimizationWhiteList;

class MreServeNeighControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testGetHeader
//     */
//    public function testGetHeader()
//    {
//        $expect = (new MreServeNeigh_day)->getVisible();
//        $user = \App\User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/LTENeighborAnalysis/LTENeighAnalysis')
//            ->seeJson(['field' => implode(',', $expect)]);
//    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        $mocks = factory(MreServeNeigh_day::class, 10)->create();
        $ecgi = $mocks->first()->ecgi;
        $dateId = date('Y-m-d', strtotime($mocks->first()->dateId));

        //create white list.
        NeighOptimizationWhiteList::create([
            'OptimizationType' => 'test',
            'dataType' => 'test',
            'city' => '常州',
            'ecgi' => $ecgi
        ]);

        //test
        $parameter = [
            'select' => '常州',
            'dataBase' => '常州',
            'city' => '常州',
            'dataType' => 'test',
            'OptimizationType' => 'test',
            'dateTime' => $dateId,
            'input1' => 1,
            'input2' => 1,
            'input3' => 1,
            'input4' => 1,
            'input5' => 1,
            'input6' => 1,
            'input7' => 1,
            'input8' => 1,
        ];

        //assert
        $expect = array();
        foreach ($mocks as $mock) {

            if ($mock->mr_LteScEarfcn == $mock->mr_LteNcEarfcn) {
                continue;
            } else if ($mock->nc_session_ratio < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1 or $mock->avg_mr_LteScRSRP < 1 or $mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->isdefined_direct == 1) {
                continue;
            } else if ($mock->dateId != $dateId) {
                continue;
            } else if ($mock->ecgi == $ecgi) {
                continue;
            }

            array_push($expect, $mock->toArray());
        }

        $user = \App\User::create(['type' => 'admin']);
        if (count($expect) == 0) {
            $expect = array('error' => 'error');
            $this->actingAs($user)->json('get', '/LTENeighborAnalysis/LTENeighAnalysisSplit', $parameter)
                ->seeJson($expect);
            return;
        }
        $this->actingAs($user)->json('get', '/LTENeighborAnalysis/LTENeighAnalysisSplit', $parameter)
            ->seeJson(['records' => $expect]);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        //create test data
        $mocks = factory(MreServeNeigh_day::class, 10)->create();
        $dateId = $mocks->first()->dateId;

        //test
        $parameter = [
            'select' => '常州',
            'dataBase' => '常州',
            'dateTime' => $dateId,
            'input1' => 1,
            'input2' => 1,
            'input3' => 1,
            'input4' => 1,
            'input5' => 1,
            'input6' => 1,
            'input7' => 1,
            'input8' => 1,
        ];

        $user = \App\User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('get', '/LTENeighborAnalysis/GSMNeighAnalysisLteAll', $parameter)
            ->decodeResponseJson();
        $fileName = $response['filename'];
        //assert file exists
        $this->assertFileExists($fileName);
        //check content.
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->mr_LteScEarfcn == $mock->mr_LteNcEarfcn) {
                continue;
            } else if ($mock->nc_session_ratio < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1 or $mock->avg_mr_LteScRSRP < 1 or $mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->isdefined_direct == 1) {
                continue;
            } else if ($mock->dateId != $dateId) {
                continue;
            }
            $mock = collect($mock->toArray())->forget(['id', 'longitude', 'latitude', 'mr_LteScEarfcn', 'mr_LteNcEarfcn', 'mr_LteNcPci', 'eventType']);
            array_push($expect, array_values($mock->toArray()));
        }

        $reader = \League\Csv\Reader::createFromPath($fileName, 'r');
        $actual = $reader->setOffset(1)->fetchAll();
        $this->assertEquals($expect, $actual);
    }
}
