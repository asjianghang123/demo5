<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\Task;
use App\User;

class StorageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testTaskQuery
     */
    public function testTaskQuery()
    {
        $mock = factory(Task::class, 10)->create();
        //get all mocked types.
        $result = $mock->unique('type')->values()->all();
        $types = array();
        foreach ($result as $item) {
            $types[] = $item->type;
        }
        //define type map.
        $typeMap = ['parameter' => 1, 'ctrsystem' => 2, 'cdrsystem' => 3, 'ebmsystem' => 4, 'pcapsystem' => 5,
            'ctrfullsystem' => 6];
        $columns = ['taskName', 'status', 'startTime', 'endTime', 'tracePath', 'owner', 'createTime', 'type'];
        //test all mocked type.
        $user = User::create(['type' => 'admin']);
//        foreach ($types as $type) {
//            $expected = $mock->where('type', $type)->toArray();
//            $this->actingAs($user)->json('get', '/storageManage/taskQuery', ['id' => $typeMap[$type]])
//                ->seeJson(['text' => implode(',', $columns), 'rows' => array_values($expected)]);
//        }
        //test all type.
        $expect = array();
        foreach($mock->toArray() as $item) {
            array_push($expect, collect($item)->forget('id')->toArray());
        }
        $this->actingAs($user)->json('get', 'storageManage/taskQuery', ['id' => 0])
            ->seeJson(['text' => implode(',', $columns), 'rows' => $expect]);
    }


    /**
     * testAddTask
     */
    public function testAddTask()
    {
        $date = date('Y-m-d H:i:s');
        $parameters = ['taskName' => 'mytask', 'type' => 'parameter', 'createTime' => $date,
            'tracePath' => 'public/common/files'];
        $user = factory(\App\User::class)->create(['user' => 'admin','type' => 'admin']);
        $this->actingAs($user)->json('post', '/storageManage/addTask', $parameters);
        $exists = Task::query()->getQuery()->where('taskName', 'mytask')->where('type', 'parameter')->where('createTime', $date)
            ->where('tracePath', 'public/common/files')->where('owner', $user->user)->exists();
        $this->assertTrue($exists);
    }

    /**
     * testDeleteTask
     */
    public function testDeleteTask()
    {
        $mock = factory(Task::class, 10)->create();
        //do not test type ctrfullsystem and pcapsystem
        $filter = $mock->whereIn('type', ['parameter', 'ctrsystem', 'cdrsystem', 'ebmsystem'])->first();
        $type = $filter->type;
        $taskName = $filter->taskName;
        $this->assertTrue(Task::query()->getQuery()->where('type', $type)->where('taskName', $taskName)->exists());

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/storageManage/deleteTask', ['type' => $type, 'taskName' => $taskName])
            ->assertResponseOk();

        $this->assertFalse(Task::query()->getQuery()->where('type', $type)->where('taskName', $taskName)->exists());
    }


    /**
     * testRunTask
     */
    public function testRunTask()
    {
        $task = factory(Task::class)->create();
        $taskName = $task->taskName;
        $tracePath = $task->tracePath;
        $startTime = $task->startTime;
        $type = $task->type;
        $task->status = 'complete';
        $expected = ['status' => 'true'];
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/storageManage/runTask', ['taskName' => $taskName, 'tracePath' => $tracePath,
            'startTime' => $startTime, 'type' => $type])->seeJson($expected);
    }

    /**
     * testStopTask
     */
    public function testStopTask()
    {
        $mock = factory(Task::class)->create();
        $user = User::create(['type' => 'admin']);
        $response = $this->actingAs($user)->json('get', '/storageManage/stopTask', ['taskName' => $mock->taskName])
            ->decodeResponseJson();
        $this->assertEquals($response['status'], 'abort');
    }

}
