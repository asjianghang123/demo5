<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Csv\Reader;
use App\Models\MR\MroServeNeigh_day;
use App\Models\Mongs\NeighOptimizationWhiteList;

class MRONeighAnalysisControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testGetHeader
//     */
//    public function testGetHeader()
//    {
//        $expect = (new MroServeNeigh_day())->getVisible();
//        $user = \App\User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/MROServeNeighAnalysis/getMroServeNeighDataHeader')
//            ->seeJson(['field' => implode(',', $expect)]);
//    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        //create test data.
        $mocks = factory(MroServeNeigh_day::class, 10)->create();
        $dateTime = $mocks->first()->dateId;
        $ecgi = $mocks->first()->ecgi;
        NeighOptimizationWhiteList::create([
            'ecgi' => $ecgi,
            'OptimizationType' => 'test',
            'dataType' => 'test',
            'city' => '常州',
        ]);

        //create expect
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->nc_session_ratio < 1) {
                continue;
            } else if ($mock->nc_times_ratio < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->dateId != $dateTime) {
                continue;
            } else if ($mock->ecgi == $ecgi) {
                continue;
            }
            array_push($expect, $mock->toArray());
        }

        //test
        $parameter = [
            'dataBase' => '常州',
            'OptimizationType' => 'test',
            'dataType' => 'test',
            'dateTime' => $dateTime,
            'input9' => 1,
            'input10' => 1,
            'input11' => 1,
            'input12' => 1,
            'input13' => 1,
            'input14' => 1
        ];

        $user = \App\User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/MROServeNeighAnalysis/getMroServeNeighData', $parameter)
            ->seeJson(['records' => $expect]);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        $mocks = factory(MroServeNeigh_day::class, 10)->create();
        $dateId = $mocks->first()->dateId;

        $parameter = [
            'dataBase' => '常州',
            'dateTime' => $dateId,
            'OptimizationType' => 'test',
            'dataType' => 'test',
            'input9' => 1,
            'input10' => 1,
            'input11' => 1,
            'input12' => 1,
            'input13' => 1,
            'input14' => 1
        ];

        $user = \App\User::create(['type' => 'admin']);
        $response = $this->actingAs($user)
            ->json('get', '/MROServeNeighAnalysis/getAllMroServeNeighData', $parameter)
            ->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        //check contents.
        $reader = Reader::createFromPath($fileName);
        $content = $reader->setOffset(1)->fetchAll();
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->nc_session_ratio < 1) {
                continue;
            } else if ($mock->nc_session_ratio < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->avg_mr_LteScRSRP < 1) {
                continue;
            } else if ($mock->dateId != $dateId) {
                continue;
            }
            $mock = collect($mock->toArray())->forget(['id', 'longitude', 'latitude', 'mr_LteNcEarfcn', 'mr_LteScEarfcn', 'mr_LteNcPci']);
            array_push($expect, array_values($mock->toArray()));
        }

        $this->assertEquals($expect, $content);
    }
}
