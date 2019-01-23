<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\SiteLte;
use App\Models\Mongs\SiteGsm;
use App\User;
use App\Models\Mongs\FtpServerInfo;

class SiteControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testTreeQuery
     */
    public function testTreeQuery()
    {
        factory(Databaseconns::class, 10)->create();
        $expected = array();
        array_push($expected, array("id" => 0, "text" => "全部城市", "value" => "city"));
        $items = Databaseconns::query()->groupBy('cityChinese')->get();
        foreach ($items as $item) {
            array_push($expected, ['id' => $item['id'], 'text' => $item['cityChinese'], 'value' => $item['connName']]);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/siteManage/TreeQuery', ['table' => 'databaseconn', 'text' => 'cityChinese', 'value' => 'connName'])
            ->seeJson($expected);
    }

    /**
     * testQuery4G
     */
    public function testQuery4G()
    {
        $mock = factory(SiteLte::class, 2)->create();
        $city = $mock->first()->city;
        $expected = $mock->where('city', $city)->flatten()->toArray();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/siteManage/QuerySite4G', ['limit' => 10, 'value' => $city])
            ->seeJson(['records' => $expected]);
    }

    /**
     * testQuery2G
     */
    public function testQuery2G()
    {
        $mock = factory(SiteGsm::class, 10)->create();
        $city = $mock->first()->city;
        $expected = $mock->where('city', $city)->flatten()->toArray();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/siteManage/QuerySite2G', ['limit' => 10, 'value' => $city])
            ->seeJson(['records' => $expected]);
    }

//    /**
//     * testImportSiteData
//     */
//    public function testImportSiteData()
//    {
//        //test import 4G site data.
//        $columns = (new SiteLte)->getImport();
//        //use make instead of create.
//        $mock = factory(SiteLte::class, 10)->make();
//        $city = $mock->first()->city;
//        $fileName = 'public/common/files/test.csv';
//        $writer = \League\Csv\Writer::createFromPath($fileName, 'w');
//        $exporter = new \Laracsv\Export($writer);
//        $exporter->build($mock, $columns);
//
//        $date = date('Y-m-d H:i:s');
//        $user = User::create(['type' => 'admin']);
//        $this->actingAs($user)->json('post', '/siteManage/getFileContent', ['table' => 'siteManage', 'city' => $city,
//            'fileName' => 'test.csv', 'importDate' => $date])->assertResponseOk();
//        $expected = array();
//        foreach ($mock->toArray() as $item) {
//            $item['city'] = $city;
//            $item['importDate'] = $date;
//            array_push($expected, collect($item)->only($columns)->toArray());
//        }
//        $this->assertEquals($expected, SiteLte::query()->get($columns)->toArray());
//
//        //todo:test import 2G site data.
//    }

    /**
     * testDownloadSiteData
     */
//     public function testDownloadSiteData()
//     {
//         //test export 4G data
//         $mock = factory(SiteLte::class, 10)->create();
//         $city = $mock->first()->city;
//         $user = User::create(['type' => 'admin']);
//         $response = $this->actingAs($user)->json('get', '/siteManage/downloadFile', ['table' => 'siteManage', 'city' => $city])
//             ->decodeResponseJson();
//         $fileName = $response['fileName'];
//         //assert file was exist.
//         $this->assertFileExists($fileName);
//         //check file content.
//         $reader = \League\Csv\Reader::createFromPath($fileName);
//         //fetch data
//         $actual = $reader->setOffset(1)->fetchAll();
//         //assert
// //        $columns = ['ecgi', 'cellName', 'cellNameChinese', 'siteName', 'siteNameChinese', 'duplexMode', 'rsi', 'tac',
// //            'longitude', 'latitude', 'dir', 'pci', 'earfcn', 'siteType', 'cellType', 'tiltM', 'tiltE', 'antHeight',
// //            'dualBandNetwork', 'CANetwork', 'address', 'band', 'channelBandWidth', 'noofTxAntennas(Site)',
// //            'highTraffic', 'highInterference', 'HST', 'cluster', 'subNetwork', 'currentOSS', '覆盖属性', 'city',
// //            'importDate'];
//         $expected = array();
//         $mock = $mock->where('city', $city)->toArray();
//         foreach ($mock as $item) {
//             //disappear some columns
//             $cal = collect($item)->forget(['id', 'longitudeBD', 'latitudeBD']);
//             array_push($expected, array_values($cal->toArray()));
//         }
//         $this->assertEquals($expected, $actual);

//         //todo:test export 2G site data.
//     }

    /**
     * testDownloadTemplateData
     */
//     public function testDownloadTemplateData()
//     {
//         //test export 4G template data.
//         $mock = factory(SiteLte::class, 10)->create();
//         $city = $mock->first()->city;
//         $user = User::create(['type' => 'admin']);
//         $response = $this->actingAs($user)->json('get', '/siteManage/downloadTemplateFile', ['table' => 'siteManage', 'city' => $city])
//             ->decodeResponseJson();
//         $fileName = $response['fileName'];
//         $this->assertFileExists($fileName);

//         //check file content.
//         $reader = \League\Csv\Reader::createFromPath($fileName, 'r');
//         $actual = $reader->setOffset(1)->fetchAll();
// //        $columns = ['cellName', 'cellNameChinese', 'siteName', 'siteNameChinese', 'duplexMode', 'longitude', 'latitude',
// //            'dir', 'siteType', 'cellType', 'tiltM', 'tiltE', 'antHeight', 'dualBandNetwork', 'HST', 'cluster',
// //            '覆盖属性'];
//         $cal = $mock->where('city', $city)->toArray();
//         $expected = array();
//         foreach ($cal as $item) {
//             $item = collect($item)->forget(['id', 'ecgi', 'rsi', 'tac', 'longitudeBD', 'latitudeBD', 'pci', 'earfcn',
//                 'CANetwork', 'address', 'band', 'channelBandWidth', 'noofTxAntennas(Site)', 'highTraffic',
//                 'highInterference', 'subNetwork', 'currentOSS', 'city', 'importDate']);
//             array_push($expected, array_values($item->toArray()));
//         }

//         $this->assertEquals($expected, $actual);
//         //todo:test export 2g template data
//     }

    /**
     * testQueryIP
     */
    public function testQueryIP()
    {
        $mock = factory(FtpServerInfo::class, 10)->create();
        $city = $mock->first()->city;
        $expected = $mock->where('city',$city)->where('type','kget')->flatten()->toArray();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/siteManage/QueryIP', ['limit' => 10, 'city' => $city])
            ->seeJson(['records' => $expected]);
    }
}
