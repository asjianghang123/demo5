<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\MR\MroPciMod3;

class PCIMOD3AnalysisControllerTest extends TestCase
{
    use DatabaseMigrations;

//    /**
//     * testGetHeader
//     */
//    public function testGetHeader()
//    {
//        $expect = (new MroPciMod3)->getVisible();
//        $user = \App\User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('post', '/PCIMOD3Analysis/getMroPCIMOD3DataHeader')->seeJson($expect);
//    }

    /**
     * testGetData
     */
    public function testGetData()
    {
        $mocks = factory(MroPciMod3::class, 10)->create();
        $dateId = $mocks->first()->dateId;

        $user = \App\User::create(['type' => 'admin']);

        $this->actingAs($user)->json('post', '/PCIMOD3Analysis/getMroPCIMOD3Data', ['dataBase' => '常州', 'dateTime' => $dateId])
            ->seeJson(['records' => $mocks->toArray()]);
    }

    /**
     * testExportData
     */
    public function testExportData()
    {
        $mocks = factory(MroPciMod3::class, 2)->create()->sortBy('userLabel');
        $dateId = $mocks->first()->dateId;

        $user = \App\User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('post', '/PCIMOD3Analysis/getAllMroPCIMOD3Data', ['dataBase' => '常州',
            'dateTime' => $dateId])->decodeResponseJson();
        $fileName = $response['filename'];
        $this->assertFileExists($fileName);

        //check contents.
        $reader = \League\Csv\Reader::createFromPath($fileName);
        $content = $reader->setOffset(1)->fetchAll();
        $expect = array();
        foreach ($mocks->toArray() as $item) {
            $item = collect($item)->forget('id')->toArray();
            array_push($expect, array_values($item));
        }

        $this->assertEquals($expect, $content);
    }
}
