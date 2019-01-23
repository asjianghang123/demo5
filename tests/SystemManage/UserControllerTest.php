<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\UserGroup;
use App\Models\Mongs\Template;
use App\Models\Mongs\Template_2G;
use App\Models\Mongs\TemplateNbi;
use App\Models\Mongs\Kpiformula;
use App\Models\Mongs\Kpiformula2G;
use App\Models\Mongs\KpiformulaNbi;
use App\User;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testTreeQuery
     */
    public function testTreeQuery()
    {
        $mock = factory(UserGroup::class, 3)->create();
        $expect = array(['id' => 0, 'text' => '全部类型', 'value' => 'type']);
        $index = 1;
        foreach ($mock as $item) {
            array_push($expect, ['id' => $index++, 'text' => $item->type, 'value' => $item->type]);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/userManage/treeQuery')->seeJson($expect);
    }

    /**
     * testTemplateQuery
     */
    public function testTemplateQuery()
    {
        $mock = factory(\App\Models\Mongs\Users::class, 10)->create();
        $type = $mock->first()->type;
        $user = User::create(['type' => 'admin']);
        $expected = $mock->where('type', $type)->flatten()->toArray();
        $this->actingAs($user)->json('get', '/userManage/templateQuery', ['type' => $type])
            ->seeJson(['records' => $expected]);
    }

    /**
     * testDeleteUser
     */
    public function testDeleteUser()
    {
        $mock = factory(\App\User::class)->create();
        $templates = factory(Template::class, 10)->create();
        $templates2G = factory(Template_2G::class, 10)->create();
        $templatesNbi = factory(TemplateNbi::class, 10)->create();
        $kpiFormulas = factory(Kpiformula::class, 10)->create();
        $kpiFormulas2G = factory(Kpiformula2G::class, 10)->create();
        $kpiFormulasNbi = factory(KpiformulaNbi::class, 10)->create();

        $id = $mock->id;
        $user = $mock->user;

        $templateExist = $templates->where('user', $user)->count() > 0;
        $template2GExist = $templates2G->where('user', $user)->count() > 0;
        $templatesNbiExist = $templatesNbi->where('user', $user)->count() > 0;
        $kpiFormulaExist = $kpiFormulas->where('user', $user)->count() > 0;
        $kpiFormula2GExist = $kpiFormulas2G->where('user', $user)->count() > 0;
        $kpiFormulasNbiExist = $kpiFormulasNbi->where('user', $user)->count() > 0;

        $this->assertTrue(User::where('id', $id)->exists());
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/userManage/deleteUser', ['id' => $id])->assertResponseOk();

        if ($templateExist) {
            $this->assertFalse(Template::where('user', $user)->exists());
        }

        if ($template2GExist) {
            $this->assertFalse(Template_2G::where('user', $user)->exists());
        }

        if ($templatesNbiExist) {
            $this->assertFalse(TemplateNbi::where('user', $user)->exists());
        }

        if ($kpiFormulaExist) {
            $this->assertFalse(Kpiformula::where('user', $user)->exists());
        }

        if ($kpiFormula2GExist) {
            $this->assertFalse(Kpiformula2G::where('user', $user)->exists());
        }

        if ($kpiFormulasNbiExist) {
            $this->assertFalse(KpiformulaNbi::where('user', $user)->exists());
        }
    }

    /**
     * testUpdateUser
     */
    public function testUpdateUser()
    {
        $user = factory(\App\User::class)->create();
        $parameter = ['id' => $user->id, 'userName' => 'test', 'name' => 'test', 'password' => 'test',
            'type' => 'test', 'email' => 'test', 'province' => 'test', 'operator' => 'test'];

        //test update
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/userManage/updateUser', $parameter)->assertResponseOk();
        $this->assertTrue(\App\User::where('type', 'test')->exists());

        //test create
        $parameter['id'] = 2;
        $parameter['type'] = 'test2';
        $this->assertFalse(\App\User::where('type', 'test2')->exists());
        $this->actingAs($user)->json('get', '/userManage/updateUser', $parameter)->assertResponseOk();
        $this->assertTrue(\App\User::where('type', 'test2')->exists());
    }

    /**
     * testGetType
     */
    public function testGetType()
    {
        $mock = factory(UserGroup::class, 4)->create();
        $expect = array();

        foreach ($mock as $item) {
            $type = $item->type;
            if ($type != 'kpionly' && $type != 'unaudited') {
                $expect[$type] = $type;
            }
        }

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/userManage/getType')->seeJson($expect);
    }

    /**
     * testUpdateUserType
     */
    public function testUpdateUserType()
    {
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/userManage/updateUserType', ['userType' => 'test'])
            ->assertResponseOk();
        $this->assertTrue(UserGroup::where('type', 'test')->exists());
    }

    /**
     * testDeleteUserType
     */
    public function testDeleteUserType()
    {
        $mock = factory(UserGroup::class)->create();
        $type = $mock->type;
        $user = factory(User::class)->create();
        $user->type = $type;
        $user->save();

        $this->assertTrue(UserGroup::where('type', $type)->exists());
        $this->assertTrue(User::where('type', $type)->exists());

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/userManage/deleteUserType', ['type' => $type])->assertResponseOk();
        $this->assertFalse(UserGroup::where('type', $type)->exists());
        $this->assertFalse(User::where('type', $type)->exists());
    }

    public function testUpdatePermission()
    {
        $mock = factory(UserGroup::class)->create();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/userManage/updatePermission', ['type' => $mock->type, 'menus' => 'test'])
            ->assertResponseOk();
        $this->assertTrue(UserGroup::where('menu_id', 'test')->exists());
    }

}
