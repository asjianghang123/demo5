<?php

/**
 * NoticeController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\UserGroup;
use App\Models\Mongs\Notification;

/**
 * 通知管理
 * Class NoticeController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NoticeController extends Controller
{


    /**
     * 获得当前通知
     *
     * @return mixed
     */
    public function getNotice()
    {
        // 获取用户组id
        $row = UserGroup::query()->get();
        $userGroupIdArr = array();
        foreach ($row as $qr) {
            $userGroupIdArr[$qr['id']] = $qr['type'];
        }

        $row = Notification::query()
                    ->get(['id','publishTime','title','content','userGroup'])
                    ->sortByDesc('publishTime');
        $items = array();
        foreach ($row as $qr) {
            $userGroupStr     = $qr['userGroup'];
            $userGroups       = explode(",", $userGroupStr);
            $userGroupChinese = [];
            foreach ($userGroups as $userGroup) {
                array_push($userGroupChinese, $userGroupIdArr[$userGroup]);
            }

            $userGroupChinese = implode(",", $userGroupChinese);
            $qr['userGroup']  = $userGroupChinese;
            array_push($items, $qr);
        }

        $result         = array();
        $result['text'] = 'id,publishTime,title,content,userGroup';
        $result['rows'] = $items;
        return json_encode($result);

    }//end getNotice()


    /**
     * 删除通知
     *
     * @return string
     */
    public function deleteNotice()
    {
        $id  = input::get("id");
        Notification::destroy($id);
        return 'true';
    }//end deleteNotice()


    /**
     * 获得用户组
     *
     * @return mixed
     */
    public function getUserGroupById()
    {
        $id           = input::get("id");
        $userGroupStr = Notification::query()->where("id", $id)->first()->userGroup;
        $userGroups   = explode(",", $userGroupStr);

        $row = UserGroup::query()->get(['id','type']);
        $items = array();
        foreach ($row as $qr) {
            $temp = array(
                     "label" => $qr['type'],
                     "value" => $qr['id'],
                    );
            foreach ($userGroups as $userGroup) {
                if ($userGroup == $qr['id']) {
                    $temp['selected'] = true;
                    break;
                }
            }
            array_push($items, $temp);
        }
        return json_encode($items);
    }//end getUserGroupById()


    /**
     * 获得所有通知
     *
     * @return mixed
     */
    public function getAllNotice()
    {
        $user_type = Auth::user()->type;
        $userGroupId = UserGroup::query()->where('type', $user_type)->first()->id;
        $row = Notification::query()->get()->sortByDesc('publishTime');
        $items = array();
        foreach ($row as $qr) {
            $userGroupStr = $qr['userGroup'];
            $userGroups     = explode(",", $userGroupStr);
            $userGroup_flag = false;
            foreach ($userGroups as $userGroup) {
                if ($userGroup == $userGroupId) {
                    $userGroup_flag = true;
                    break;
                }
            }
            if (!$userGroup_flag) {
                continue;
            }
            array_push($items, $qr);
        }
        return json_encode($items);
    }//end getAllNotice()
}//end class
