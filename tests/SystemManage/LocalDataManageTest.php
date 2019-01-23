<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Models\Mongs\Task;

class LocalDataManageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testAddTask
     */
    public function testAddTask()
    {
        $task = factory(Task::class, 1)->create();
        $taskName = $task->taskName;

        //test add task with an exist taskName
        $user = User::create(['type' => 'admin', 'user' => 'admin']);
        $this->actingAs($user)->json('post', '/dataSourceManage/addTask', ['taskName' => $taskName])->seeJson(['state' => 1]);
        //test add task with non exist taskName
        $this->actingAs($user)->json('post', '/dataSourceManage/addTask',
            ['taskName' => $taskName . '1', 'type' => 'kget', 'createTime' => date('Y-m-d H:i:s'), 'tracePath' => '/temp'])->seeJson(['state' => 0]);

        //assert item was inserted into database
        $this->assertTrue(Task::query()->getQuery()->where('taskName', $taskName . '1')->exists());
    }
}
