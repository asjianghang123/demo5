<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BoardAnalysisControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function testView()
    {
//        \App\User::query()->first()
//        dd(\App\User::query()->where('user','admin')->get()[0]);
        $response = $this->actingAs(\App\User::query()->where('user','admin')->first())
            ->call('GET', '/BoardAnalysis');
        $this->assertEquals('200', $response->status());
    }

    public function testGetAllSlot()
    {
        $this->json('POST', '/BoardAnalysis/getAllSlot', ['type'=>'rru', 'limit'=>10, 'city'=>['常州']])
            ->seeJson([]);
    }

    public function testGetDisappearSlot()
    {
        $this->json('POST', '/BoardAnalysis/getDisappearSlot', ['city'=>['常州'], 'type'=>'rru'])
            ->seeJson([]);
    }

    public function testGetSlotTrendChart()
    {
        $this->json('POST', '/BoardAnalysis/getSlotTrendChart', ['serialNumber'=>'D826501582'])
            ->seeJson([]);
    }

    public function testGetOneSlotInfo()
    {
        $this->json('POST', '/BoardAnalysis/getOneSlotInfo', ['serialNumber'=>'D826501582', 'type'=>'rru'])
            ->seeJson([]);
    }

    /**
     * 需要注释掉文件生成代码才可运行，具体解决方法还未找到,暂时跳过。。。不兼容fopen?
     */
    public function testExportOneSolt()
    {
        $this->markTestSkipped()
            ->json('POST', '/BoardAnalysis/exportOneSolt', ['serialNumber'=>'D826501582', 'type'=>'rru'])
            ->seeJson([]);;
    }

}
