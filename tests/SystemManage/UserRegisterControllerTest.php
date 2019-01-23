<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class UserRegisterControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testUserRegister
     */
    public function testUserRegister()
    {
        $this->json('post', '/userRegister/userRegister', ['name' => 'test', 'email' => 'test',
            'password' => 'test'])->assertResponseOk();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->assertTrue(User::where('name', 'test')->where('type', 'unaudited')->exists());
    }

}
