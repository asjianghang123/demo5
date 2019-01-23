<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FacadesTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        /**
         * 测试方法
         * $oss = 'oss';
         * return SubnetWork::getSubnetWork($oss);
         */
        SubnetWork::shouldReceive('getSubnetWork')->once()->with('oss')->andReturn('oss');
        $response = $this->call('GET', '/LTEQuery/getLTETreeData');
        $this->assertEquals(200, $response->status());
        //        $this->get('/LTEQuery/getLTETreeData');
    }
}
