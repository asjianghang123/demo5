<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\AutoKPI\RelationNonHandover;
use App\User;

class RelationNonHandoverTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testGetHeader
     */
    public function testGetHeader()
    {
        $user = User::create(['type' => 'admin']);
        $columns = (new RelationNonHandover)->getVisible();
        $expect = ['text' => implode(',', $columns)];
        $this->actingAs($user)->json('get', '/relationNonHandover/getDataHeader')->seeJson($expect);
    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        $mocks = factory(RelationNonHandover::class, 10)->create();
        $date = $mocks->first()->day_to;
        $city = $mocks->first()->city;

        $expect = $mocks->where('day_to', $date)->where('city', $city)->toArray();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/relationNonHandover/getTableData', ['date' => $date, 'city' => $city])
            ->seeJson($expect);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        //create mock data.
        $mocks = factory(RelationNonHandover::class, 10)->create();

        //define parameter.
        $date = $mocks->first()->day_to;
        $city = $mocks->first()->city;

        //test.
        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('post', '/relationNonHandover/getAllTableData', [
            'date' => $date, 'city' => $city
        ])->decodeResponseJson();
        $fileName = $response['filename'];

        //check file exists.
        $this->assertFileExists($fileName);

        //check content.
        $reader = \League\Csv\Reader::createFromPath($fileName);
        $content = $reader->setOffset(1)->fetchAll();
        $result = $mocks->where('day_to', $date)->where('city', $city)->toArray();
        $expect = array();
        foreach ($result as $item) {
            array_push($expect, array_values($item));
        }
        $this->assertEquals($expect, $content);
    }
}
