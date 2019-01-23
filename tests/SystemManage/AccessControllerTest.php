<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\AccessDetail;
use App\User;
class AccessControllerTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * testGetAccessData
     *
     * @return void
     */
    public function testGetAccessData()
    {
        $mock = factory(AccessDetail::class, 5)->create();
        $grouped = $mock->groupBy('url');
        $expected = array();
        foreach ($grouped as $key => $value) {
            $expected[] = ['url' => $key, 'urlChinese' => $value[0]->urlChinese, 'sum' => count($value)];
        }
        $startDate = $mock->pluck('date_id')->min();
        $endDate = $mock->pluck('date_id')->max();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/accessManage/getAccessData',
            ['startDate' => $startDate, 'endDate' => $endDate])
            ->seeJson(['records' => $expected]);
    }

    /**
     * testDownloadAccessData
     *
     * @return void
     */
    public function testDownloadAccessData()
    {
        $mock = factory(AccessDetail::class, 10)->create();
        $grouped = $mock->groupBy('url');
        $expected = array();
        foreach ($grouped as $key => $value) {
            $expected[] = [$key, $value[0]->urlChinese, count($value)];
        }
        $expected = array_values(collect($expected)->sortByDesc(2)->toArray());
        $startDate = $mock->pluck('date_id')->min();
        $endDate = $mock->pluck('date_id')->max();
        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('post', '/accessManage/downloadAccessData',
            ['startDate' => $startDate, 'endDate' => $endDate])->decodeResponseJson();
        $response = collect($response);
        $fileName = $response->get('filename');

        $this->assertNotEmpty($fileName);
        $this->assertFileExists($fileName);

        $reader = \League\Csv\Reader::createFromPath($fileName);
        $this->assertEquals(['url', 'urlChinese', 'sum'], $reader->fetchOne());

        $this->assertEquals($expected, $reader->setOffset(1)->fetchAll());
    }

}
