<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\AutoKPI\RelationBadHandover;
use League\Csv\Reader;

class RelationBadHandoverControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testGetHeader
     */
    public function testGetHeader()
    {
        $user = \App\User::create(['type' => 'admin']);
        $expect = (new RelationBadHandover)->getVisible();
        $this->actingAs($user)->json('post', '/relationBadHandover/getDataHeader')
            ->seeJson(['text' => implode(',', $expect)]);
    }

    /**
     * testGetData
     */
    public function testGetData()
    {

        $mocks = factory(RelationBadHandover::class, 10)->create();
        $dateId = $mocks->first()->day_to;
        $city = $mocks->first()->city;

        $expect = $mocks->where('day_to', $dateId)->where('city', $city)->flatten(1)->toArray();

        //order problems.
        $user = \App\User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/relationBadHandover/getTableData', ['city' => $city, 'date' => $dateId])
            ->seeJson(['records' => $expect]);
    }

    /**
     * tetExportData
     */
    public function testExportData()
    {
        $mocks = factory(RelationBadHandover::class, 10)->create();
        $dateId = $mocks->first()->day_to;
        $city = $mocks->first()->city;

        $user = \App\User::create(['type' => 'admin']);
        $response = $this->actingAs($user)
            ->json('post', '/relationBadHandover/getAllTableData', ['city' => $city, 'date' => $dateId])
            ->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        //check contents.
        $reader = Reader::createFromPath($fileName);
        $content = $reader->setOffset(1)->fetchAll();

        $results = $mocks->where('city', $city)->where('day_to', $dateId)->toArray();
        $expect = array();
        foreach ($results as $item) {
            array_push($expect, array_values($item));
        }
        $this->assertEquals($expect, $content);
    }
}
