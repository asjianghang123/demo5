<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\QueryAnalysis\LTEQueryController;
use App\Models\Mongs\AlarmInfo;
use App\User;
use App\Models\Mongs\Template;
class LTEQueryControllerTest extends TestCase
{
    use WithoutMiddleware;


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetTreeData()
    {
//        $mock = factory(Template::class, 3)->create();
        $user = User::create(['type' => 'admin']);
        $templateNames = Template::where('user', $user->type)->orderBy('templateName', 'asc')->get();
        foreach ($templateNames as $key=>$value) {
            $expected['nodes'][] = ['text' => $value['templateName'], 'id' => $value['id']];
        }
        $expected['text'] = "通用模板";
//        dd(json_encode($expected));
        $this->actingAs($user)->json('GET', '/LTEQuery/getLTETreeData')
            ->seeJson([
                $expected
            ]);
    }


    public function testSearchLTETreeData()
    {
        $expect = array();
        $mock = factory(Template::class, 2)->create();
        $templateName = $mock->first()->templateName;
        $templateId = $mock->first()->id;

        $expect['nodes'][0]['text'] = $templateName;
        $expect['nodes'][0]['id']   = $templateId;
        $expect['text'] = '通用模板';
        $users = 'test_' . date('Ymd') . '_' . uniqid();
        $user = User::create(['type'=>'admin', 'user'=>$users]);
        $this->actingAs($user)
            ->json('GET','/LTEQuery/searchLTETreeData', ['inputData'=>$templateName])
            ->seeJson([
                $expect
            ]);
    }

    public function testGetFormatAllSubNetwork()
    {
        $this->json('GET', '/LTEQuery/getAllSubNetwork', ['citys'=>['常州'], 'format'=>'TDD'])
        ->seeJson([ "{\"text\":\"ChangZhou\",\"value\":\"ChangZhou\"}","{\"text\":\"Changzh2TDDG2\",\"value\":\"Changzh2TDDG2\"}","{\"text\":\"Changzhou10_LTE\",\"value\":\"Changzhou10_LTE\"}","{\"text\":\"Changzhou11_LTE\",\"value\":\"Changzhou11_LTE\"}","{\"text\":\"Changzhou12_LTE\",\"value\":\"Changzhou12_LTE\"}","{\"text\":\"Changzhou13_LTE\",\"value\":\"Changzhou13_LTE\"}","{\"text\":\"Changzhou14_LTE\",\"value\":\"Changzhou14_LTE\"}","{\"text\":\"Changzhou1_LTE\",\"value\":\"Changzhou1_LTE\"}","{\"text\":\"Changzhou1_TDG2\",\"value\":\"Changzhou1_TDG2\"}","{\"text\":\"Changzhou2_LTE\",\"value\":\"Changzhou2_LTE\"}","{\"text\":\"Changzhou3_LTE\",\"value\":\"Changzhou3_LTE\"}","{\"text\":\"Changzhou4_LTE\",\"value\":\"Changzhou4_LTE\"}","{\"text\":\"Changzhou5_LTE\",\"value\":\"Changzhou5_LTE\"}","{\"text\":\"Changzhou6_LTE\",\"value\":\"Changzhou6_LTE\"}","{\"text\":\"Changzhou7_LTE\",\"value\":\"Changzhou7_LTE\"}","{\"text\":\"Changzhou8_LTE\",\"value\":\"Changzhou8_LTE\"}","{\"text\":\"Changzhou9_LTE\",\"value\":\"Changzhou9_LTE\"}","{\"text\":\"ChangzhouA_LTE\",\"value\":\"ChangzhouA_LTE\"}","{\"text\":\"ChangzhouB_LTE\",\"value\":\"ChangzhouB_LTE\"}","{\"text\":\"ChangzhouC_LTE\",\"value\":\"ChangzhouC_LTE\"}","{\"text\":\"ChangzhouD_LTE\",\"value\":\"ChangzhouD_LTE\"}","{\"text\":\"ChangzhouE_LTE\",\"value\":\"ChangzhouE_LTE\"}","{\"text\":\"ChangzhouF_LTE\",\"value\":\"ChangzhouF_LTE\"}","{\"text\":\"ChangzhouG_LTE\",\"value\":\"ChangzhouG_LTE\"}","{\"text\":\"Changzhou_NBFdd\",\"value\":\"Changzhou_NBFdd\"}" ]);
    }

    /*public function testTemplateQuery()
    {
        $this->json('POST', '/LTEQuery/templateQuery',
            [   'template'=>'1125丢包',
                'locationDim'=>'cell',
                'timeDim'=>'day',
                'startTime'=>'2018-07-09',
                'endTime'=>'2018-07-09',
                'hour'=>'null',
                'minute'=>'null',
                'city'=>['镇江'],
                'subNet'=>["ZhenJiang","Zhenjiang_LTE","Zhenjiang2_LTE","Zhenjiang3_LTE","Zhenjiang4_LTE","Zhenjiang_TDG2"],
                'erbs'=>'',
                'cell'=>'',
                'format'=>'TDD',
                'style'=>'online',
                'parent'=>'通用模板'
            ])->seeJson([]);
    }*/

}
