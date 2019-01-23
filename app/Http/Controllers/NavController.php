<?php

/**
 * NavController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Config;
use App\Models\Mongs\UserGroup;
use App\Models\Mongs\Notification;
use App\Models\Mongs\MenuList;
use App\Models\Mongs\AccessDetail;
use App\Models\Mongs\Sessions;

/**
 * 系统管理
 * Class NavController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NavController extends Controller
{
    /**
     * 语言切换
     *
     * @param Request $request HTTP请求
     *
     * @return Request
     */
    public function localeLang(Request $request)
    {
        $lang = Input::get('lang');
        $request->session()->put('language', $lang);
        return $lang;
    }
    
    /**
     * 获得用户信息
     *
     * @return void
     */
    public function getUser()
    {
        if (!Auth::user()) {
            echo "login";
            return;
        }

        $user = Auth::user();
        echo json_encode($user);

    }//end getUser()

    /**
     * 登出
     *
     * @return void
     */
    public function signout()
    {
        if (Auth::check()) {
            Auth::logout();
        }

        echo "success";

    }//end signout()


    /**
     * 获取平台每小时的session数
     *
     * @return bool|int
     */
    public function getSessions()
    {
        date_default_timezone_set('PRC');
        $year     = Input::get('year');
        $mon      = Input::get('mon');
        $day      = Input::get('day');
        $hour     = Input::get('hour');
        $currTime = mktime($hour, 0, 0, $mon, $day, $year);
        $day      = date('Y-m-d', $currTime);
        $hour     = date('H', $currTime);
        $currDate = date('Y-m-d H', $currTime);
        $isGenius = Input::get('isGenius');
        $href     = Input::get('href');
        if ($isGenius == "genius" && $href != "home") {
            $user = Auth::user()->user;
            $createTime = date('Y-m-d H:i:s');

            $access_detail = new AccessDetail;
            $access_detail->date_id = $day;
            $access_detail->user = $user;
            $access_detail->url = $href;
            $access_detail->createTime = $createTime;
            $access_detail->save();
        }

        $dirname = '/opt/lampp/htdocs/genius/storage/framework/sessions';
        $filenum = 0;
        if (!file_exists($dirname)) {
            return false;
        }

        $dir = opendir($dirname);
        while ($filename = readdir($dir)) {
            $newfile = $dirname.'/'.$filename;
            if (!is_dir($newfile)) {
                $fileTime  = filemtime($newfile);
                $filectime = date("Y-m-d H", $fileTime);
                if ($currDate == $filectime) {
                    $filenum++;
                }
            }
        }
        Sessions::destroy($currDate);
        $sessions = new Sessions;
        $sessions->id = $currDate;
        $sessions->date = $day;
        $sessions->hour = $hour;
        $sessions->num = $filenum;
        $sessions->save();
        return $filenum;

    }//end getSessions()


    /**
     * 新增通知
     *
     * @return void
     */
    public function addNotice()
    {
        $title     = input::get("title");
        $content   = input::get("content");
        $id        = input::get("id");
        $userGroup = input::get("userGroup");

        $user = Auth::user()->user;
        date_default_timezone_set("PRC");
        $time = date('YmdHis');
        if ($id) {
            $res = Notification::where('id', $id)->update(['publishTime'=>$time,
                                                            'title'=>$title,
                                                            'content'=>$content,
                                                            'userGroup'=>$userGroup]);
        } else {
            $notification = new Notification;
            $notification->publishTime = $time;
            $notification->publisher = $user;
            $notification->title = $title;
            $notification->content = $content;
            $notification->userGroup = $userGroup;
            $res = $notification->save();
        }
        print_r($res);

    }//end addNotice()


    /**
     * 获取通知
     *
     * @return void
     */
    public function getNotice()
    {
        $user      = Auth::user()->id;
        $user_type = Auth::user()->type;
        // 获取用户所在用户组id
        $userGroupId = UserGroup::query()->where('type', $user_type)->first()->id;
        $row = Notification::query()->get()->sortByDesc('publishTime');
        $items = array();
        foreach ($row as $qr) {
            $userGroupStr = $qr['userGroup'];
            $readedStr    = $qr['readed'];

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

            if ($readedStr) {
                $readeds     = explode(",", $readedStr);
                $readed_flag = true;
                foreach ($readeds as $readed) {
                    if ($readed == $user) {
                        $readed_flag = false;
                        break;
                    }
                }

                if ($readed_flag) {
                    array_push($items, $qr);
                }
            } else {
                array_push($items, $qr);
            }
        }//end foreach

        echo json_encode($items);

    }//end getNotice()


    /**
     * 标记通知已读
     *
     * @return void
     */
    public function readNotice()
    {
        $user = Auth::user()->id;
        $id   = input::get("id");
        $row = Notification::query()->where('id', $id)->first();
        if ($row['readed'] != "") {
            $reader = $row['readed'].",".$user;
        } else {
            $reader = $user;
        }
        $res = Notification::where('id', $id)->update(['readed' => $reader]);
        print_r($res);

    }//end readNotice()


    /**
     * 获得所有通知
     *
     * @return void
     */
    public function readAllNotice()
    {
        $user    = Auth::user()->id;
        $ids     = input::get("ids");
        $idArray = explode(",", $ids);
        foreach ($idArray as $id) {
            $row = Notification::query()->where('id', $id)->first();
            if ($row['readed'] != "") {
                $reader = $row['readed'].",".$user;
            } else {
                $reader = $user;
            }
            Notification::where('id', $id)->update(['readed' => $reader]);
        }
    }//end readAllNotice()


    /**
     * 获得地图选项
     *
     * @return array
     */
    public function getOption()
    {
   
        $arr = config('option.'.env('CITY'))['center'];
  
        return $arr;

    }//end getOption()


    /**
     * 获得菜单列表
     *
     * @return void
     */
    public function getMenuList()
    {
        // $user_type = Auth::user()->type;
        // $dbc       = new DataBaseConnection();
        // $db        = $dbc->getDB('mongs', 'mongs');
        // $menu_id = UserGroup::query()->where('type',$user_type)->first()->menu_id;
        // $sql       = "SELECT menu FROM menu_list WHERE id not in ($menu_id)";
        // $res       = $db->query($sql);
        // $row       = $res->fetchAll(PDO::FETCH_ASSOC);
        // $items     = array();
        // foreach ($row as $qr) {
        //     array_push($items, $qr);
        // }
        // echo json_encode($items);
    }//end getMenuList()


    /**
     * 获得用户组
     *
     * @return void
     */
    public function getUserGroup()
    {
        $row = UserGroup::query()->get(['id','type']);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, array("label" => $qr['type'], "value" => $qr['id'], "selected" => true));
        }
        echo json_encode($items);

    }//end getUserGroup()


}//end class
