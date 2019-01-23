<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Mongs\UserGroup;
use App\Models\Mongs\Notification;
use App\User;

class NotificationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * testGetNotice
     */
    public function testGetNotice()
    {
        $userGroups = factory(UserGroup::class, 4)->create();
        $notifications = factory(Notification::class, 2)->create();
        $expected = array();

        foreach ($notifications as $item) {
            $groups = $userGroups->whereInLoose('id', explode(',', $item->userGroup))->implode('type', ',');
            $expected[] = ['id' => $item->id, 'publishTime' => $item->publishTime->format('Y-m-d H:i:s'), 'title' => $item->title,
                'content' => $item->content, 'userGroup' => $groups];
        }

        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('get', '/noticeManage/getNotice')->seeJson(['rows' => $expected]);

    }

    public function testDeleteNotice()
    {
        $mock = factory(Notification::class)->create();
        $id = $mock->id;
        $user = User::create(['type' => 'admin']);
        $this->assertTrue(Notification::query()->getQuery()->where('id', $id)->exists());
        $this->actingAs($user)->json('get', '/noticeManage/deleteNotice', ['id' => $id])->assertResponseOk();
        $this->assertFalse(Notification::query()->getQuery()->where('id', $id)->exists());
    }

    public function testGetUserGroupById()
    {
        $groups = factory(UserGroup::class, 4)->create();
        $notification = factory(Notification::class)->create();
        $userGroup = explode(',', $notification->userGroup);
        $items = array();
        foreach ($groups as $group) {
            $item = array();
            $item['label'] = $group->type;
            $item['value'] = $group->id;
            if (array_search($group->id, $userGroup) !== false) {
                $item['selected'] = true;
            }
            array_push($items, $item);
        }
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/noticeManage/getUserGroupById', ['id' => $notification->id])->seeJson($items);
    }

    public function testGetAllNotice()
    {
        factory(UserGroup::class, 4)->create();
        $user = User::create(['type' => 'admin']);
        $notifications = factory(Notification::class, 4)->create()->sortByDesc('publishTime');

        //admin's id
        $userId = UserGroup::where('type','admin')->first()->id;
        $expected = array();
        foreach ($notifications as $notification) {
            $userGroup = explode(',', $notification->userGroup);
            if (array_search($userId, $userGroup) !== false) {
                //for test
                $item = array();
                $item['content'] = $notification->content;
                $item['id'] = $notification->id;
                $item['publishTime'] = $notification->publishTime->format('Y-m-d H:i:s');
                $item['publisher'] = $notification->publisher;
                $item['readed'] = $notification->readed;
                $item['title'] = $notification->title;
                $item['userGroup'] = $notification->userGroup;
                array_push($expected, $item);
                //for test
            }
        }
        $this->actingAs($user)->json('get', '/readAllNotice/getAllNotice')->seeJson($expected);
    }

}
