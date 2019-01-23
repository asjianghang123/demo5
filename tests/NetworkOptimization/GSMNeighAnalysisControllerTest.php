<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\MR\MreServerNeighIrat_day;
use App\Models\Mongs\NeighOptimizationWhiteList;
use App\User;

class GSMNeighAnalysisControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testGetHeader
//     */
//    public function testGetHeader()
//    {
//        $expected = (new MreServerNeighIrat_day())->getVisible();
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('get', '/GSMNeighborAnalysis/GSMNeighAnalysis')
//            ->seeJson(['field' => implode(',', $expected)]);
//    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        //Create mock data.
        $mocks = factory(MreServerNeighIrat_day::class, 10)->create();
        $ecgi = $mocks->first()->ecgi;
        $dateId = $mocks->first()->dateId;
        //Crate whiteList.
        NeighOptimizationWhiteList::create(['OptimizationType' => 'test', 'dataType' => 'test',
            'city' => '常州', 'ecgi' => $ecgi]);
        $input1 = $input6 = $input7 = 1;

        //create expect result
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->nc_session_ratio >= $input1 && ($mock->avg_mr_GsmNcellCarrierRSSI >= $input6 &&
                    $mock->avg_mr_LteScRSRQ >= $input7) && ($mock->isdefined != 1 or $mock->isdefined === null)
                && $mock->dateId == $dateId && $mock->ecgi != $ecgi
            ) {
                array_push($expect, $mock->toArray());
            }
        }

        //create parameter
        $parameter = [
            'select' => '常州',
            'dataBase' => '常州',
            'dataType' => 'test',
            'OptimizationType' => 'test',
            'dateTime' => date('Y-m-d', strtotime($dateId)),
            'input1' => $input1,
            'input6' => $input6,
            'input7' => $input7
        ];

        //test
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/GSMNeighborAnalysis/GSMNeighAnalysisSplit', $parameter)
            ->seeJson(['records' => $expect]);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        //Create mock data.
        $mocks = factory(MreServerNeighIrat_day::class, 2)->create();
        $dateId = $mocks->first()->dateId;
        $input1 = $input6 = $input7 = 1;
        //create expect result
        $expect = array();
        foreach ($mocks as $mock) {
            if ($mock->nc_session_ratio >= $input1 && ($mock->avg_mr_GsmNcellCarrierRSSI >= $input6 &&
                    $mock->avg_mr_LteScRSRQ >= $input7) && ($mock->isdefined != 1 || $mock->isdefined === null)
                && $mock->dateId == $dateId
            ) {
                $mock = collect($mock->toArray())->forget(['id','siteName', 'mr_LteScEarfcn', 'eventType', 'mr_GsmNcellBcch', 'mr_GsmNcellNcc', 'mr_GsmNcellBcc',
                    'cellID', 'longitude_4g', 'latitude_4g', 'longitude_2g', 'latitude_2g'])->toArray();
                array_push($expect, array_values($mock));
            }
        }
        //create parameter
        $parameter = [
            'select' => '常州',
            'dataBase' => '常州',
            'dataType' => 'test',
            'OptimizationType' => 'test',
            'dateTime' => date('Y-m-d', strtotime($dateId)),
            'input1' => $input1,
            'input6' => $input6,
            'input7' => $input7,
        ];

        //test
        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('get', '/GSMNeighborAnalysis/GSMNeighAnalysisAll', $parameter)
            ->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        $reader = \League\Csv\Reader::createFromPath($fileName);
        $this->assertEquals($expect, $reader->setOffset(1)->fetchAll());
    }
}
