<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Mongs\MailGroup;
use App\User;
use App\Models\Mongs\MailList;

class EmailControllerTest extends TestCase
{
    use DatabaseMigrations;


    /**
     * testTreeQuery
     */
    public function testTreeQuery()
    {
        //create mock data.
        $mock = factory(MailGroup::class, 10)->create();

        //generate expect result.
        $expected = array();
        $mock = $mock->groupBy('scope')->toArray();

        foreach ($mock as $key => $roles) {
            $items = array();
            $items['text'] = $key;
            $items['nodes'] = array();
            foreach ($roles as $role) {
                array_push($items['nodes'], array('text' => $role['role'], 'id' => $role['id'], 'scope' => $role['scope']));
            }
            array_push($expected, $items);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/treeQuery')->seeJson($expected);
    }

    /**
     * testGetTableData
     */
    public function testGetTableData()
    {
        //generate test data
        $mock = factory(MailList::class, 50)->create();

        //pick an scope/role to do test.
        $scope = $mock->first()->scope;
        $role = $mock->first()->role;

        //create expected
        $result = $mock->where('scope', $scope)->where('role', $role)->flatten()->toArray();
        $expected = ['total' => count($result), 'records' => $result];

        //assert
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/getTableData', ['scope' => $scope, 'role' => $role])
            ->seeJson($expected);
    }

    /**
     * testNewMail
     */
    public function testNewMail()
    {
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/insertDownload', ['mailAddress' => 'fuzzywy@163.com',
            'name' => 'yang', 'role' => 'developer', 'scope' => 'developer', 'city' => 'changzhou']);
        $data = MailList::all()->get(0);
        $this->assertEquals('fuzzywy@163.com', $data->mailAddress);
        $this->assertEquals('yang', $data->name);
        $this->assertEquals('developer', $data->role);
        $this->assertEquals('developer', $data->scope);
        $this->assertEquals('changzhou', $data->city);
    }

    /**
     * testDelMail
     */
    public function testDelMail()
    {
        //create mock data.
        $mock = factory(MailList::class, 10)->create();

        //pick one record to delete.
        $id = $mock->first()->id;

        //assert item exists in database before do delete.
        $builder = MailList::query()->where('id', $id);
        $this->assertTrue($builder->exists());

        //do test
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/deleteDownload', ['id' => $id])
            ->assertResponseOk();

        $this->assertFalse($builder->exists());
    }

    /**
     * testUpdateScope
     */
    public function testUpdateScope()
    {
        $builder = MailGroup::query()->where('scope', 'lte_kpi_15min')
            ->where('role', 'customer');
        $this->assertFalse($builder->exists());
        //do test
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/updateScope', ['scope' => 'lte_kpi_15min', 'role' => 'customer'])
            ->assertResponseOk();

        $this->assertTrue($builder->exists());
    }

    /**
     * testGetScope
     */
    public function testGetScope()
    {
        //create mock data
        MailGroup::create(['scopeName' => 'lte_kpi_15_min', 'scope' => 'lte_kpi_15_min']);
        MailGroup::create(['scopeName' => 'kget_paracheck', 'scope' => 'kget_paracheck']);
        MailGroup::create(['scopeName' => 'kgetpart_bulkcm', 'scope' => 'kgetpart_bulkcm']);

        //do test
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/getScope')->seeJson(['lte_kpi_15_min' => 'lte_kpi_15_min',
            'kget_paracheck' => 'kget_paracheck', 'kgetpart_bulkcm' => 'kgetpart_bulkcm']);
    }

    /**
     * testGetRole
     */
    public function testGetRole()
    {
        //create mock data
        MailGroup::create(['scope' => 'lte_kpi_15_min', 'roleName' => 'customer', 'role' => 'customer']);
        MailGroup::create(['scope' => 'lte_kpi_15_min', 'roleName' => 'user', 'role' => 'user']);
        MailGroup::create(['scope' => 'lte_kpi_15_min', 'roleName' => 'engineer', 'role' => 'engineer']);

        //assert
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/emailManage/getRole', ['scope' => 'lte_kpi_15_min'])->seeJson(['customer' => 'customer',
            'user' => 'user', 'engineer' => 'engineer']);
    }

    /**
     * testDeleteScope
     */
    public function testDeleteScope()
    {
        //generate test data in database
        MailGroup::create(['scope' => 'lte_kpi_15_min', 'scopeName' => 'lte_kpi_15_min', 'role' => 'customer',
            'roleName' => 'customer']);
        MailGroup::create(['scope' => 'lte_kpi_15_min', 'scopeName' => 'lte_kpi_15_min', 'role' => 'engineer',
            'roleName' => 'engineer']);
        MailGroup::create(['scope' => 'kget_paracheck', 'scopeName' => 'kget_paracheck', 'role' => 'user',
            'roleName' => 'user']);
        MailGroup::create(['scope' => 'kget_paracheck', 'scopeName' => 'kget_paracheck', 'role' => 'admin',
            'roleName' => 'admin']);


        MailList::create(['mailAddress' => 'fuzzywy@163.com', 'name' => 'yang', 'role' => 'customer',
            'scope' => 'lte_kpi_15_min', 'city' => 'changzhou']);
        MailList::create(['mailAddress' => 'fuzzywy1@163.com', 'name' => 'efjlmmo', 'role' => 'engineer',
            'scope' => 'lte_kpi_15_min', 'city' => 'wuxi']);
        MailList::create(['mailAddress' => 'leonjunliu@163.com', 'name' => 'leon', 'role' => 'user',
            'scope' => 'kget_paracheck', 'city' => 'nantong']);
        MailList::create(['mailAddress' => 'fuzzywy@hotmail.com', 'name' => 'wangyang', 'role' => 'admin',
            'scope' => 'kget_paracheck', 'city' => 'zhenjiang']);

        $user = User::create(['type' => 'admin']);
        //test delete without id.
        $this->actingAs($user)->json('post', '/emailManage/deleteScope', ['id' => 0, 'scope' => 'lte_kpi_15_min', 'role' => 'customer'])
            ->assertResponseOk();

        //assert all scope with lte_kpi_15_min was deleted
        $this->assertFalse(MailGroup::where('scope', 'lte_kpi_15_min')->exists());
        $this->assertFalse(MailList::where('scope', 'lte_kpi_15_min')->exists());

        //test delete with id
        $id = MailGroup::all('id')->min();
        $this->actingAs($user)->json('post', '/emailManage/deleteScope', ['id' => $id, 'scope' => 'kget_paracheck', 'role' => 'user'])
            ->assertResponseOk();

        $this->assertFalse(MailGroup::where('id', $id)->exists());
        $this->assertFalse(MailList::where('scope', 'kget_paracheck')->where('role', 'user')->exists());
        $this->assertTrue(MailGroup::where('scope', 'kget_paracheck')->exists());
        $this->assertTrue(MailList::where('scope', 'kget_paracheck')->exists());
        //test delete with id.
    }
}
