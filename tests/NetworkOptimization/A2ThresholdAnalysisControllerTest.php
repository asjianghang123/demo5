<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\MR\MreA2Threshold;
use App\User;
use App\Models\Mongs\Databaseconns;

class A2ThresholdAnalysisControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testGetData
     */
    public function testGetData()
    {
        $mock = factory(MreA2Threshold::class, 2)->create();
        $dateTime = date('Y-m-d', strtotime($mock->first()->datetime_id));

        $expect = array();
        foreach ($mock as $item) {
            $date = date('Y-m-d', strtotime($item->datetime_id));
            if ($date == $dateTime) {
                array_push($expect, $item->toArray());
            }
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/A2ThresholdAnalysis/getMreA2ThresholdData', ['dataBase' => '常州', 'dateTime' => $dateTime])
            ->seeJson(['records' => $expect]);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        $mock = factory(MreA2Threshold::class, 50)->create();
        $dateTime = date('Y-m-d', strtotime($mock->first()->datetime_id));

        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('get', '/A2ThresholdAnalysis/getAllMreA2ThresholdData', ['dataBase' => '常州',
            'dateTime' => $dateTime])->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        $expect = array();
        foreach ($mock as $item) {
            $date = date('Y-m-d', strtotime($item->datetime_id));
            if ($date == $dateTime) {
                array_push($expect, array_values($item->toArray()));
            }
        }

        $reader = \League\Csv\Reader::createFromPath($fileName);
        $this->assertEquals($expect, $reader->setOffset(1)->fetchAll());
    }
}
