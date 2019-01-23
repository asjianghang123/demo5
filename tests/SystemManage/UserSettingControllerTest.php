<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class UserSettingControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testUpdateUser
     */
    public function testUpdateUser()
    {
        $user = factory(User::class)->create();
        $id = $user->id;
        $name = $user->name;
        $email = $user->email . 'update';
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/UserSetting/updateUser', ['id' => $id, 'name' => $name, 'email' => $email])
            ->assertResponseOk();
        $this->assertTrue(User::where('id', $id)->where('email', $email)->exists());

    }

    /**
     * testUpdatePassword
     */
    public function testUpdatePassword()
    {
        $user = factory(User::class)->create();
        $id = $user->id;
        $password = $user->pwd . 'update';

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', 'UserSetting/updatePassword', ['id' => $id, 'password' => $password])
            ->assertResponseOk();
        $this->assertTrue(User::where('id', $id)->where('pwd', $password)->exists());

    }
}
