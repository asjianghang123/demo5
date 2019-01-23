<?php

/**
 * UserController.php
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
use Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Mongs\Users;
use App\Models\Mongs\UserGroup;
use App\Models\Mongs\MenuList;
use App\Models\Mongs\Template;
use App\Models\Mongs\Template_2G;
use App\Models\Mongs\TemplateNbi;
use App\Models\Mongs\Kpiformula;
use App\Models\Mongs\Kpiformula2G;
use App\Models\Mongs\KpiformulaNbi;

/**
 * 用户管理
 * Class UserController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class UserController extends Controller
{


    /**
     * 获得角色列表
     *
     * @return mixed
     */
    public function treeQuery()
    {
        $row = UserGroup::get()->toArray();
        $types = array();
        array_push($types, array("id" => 0, "text" => "全部类型", "value" => "type"));
        $i = 1;
        foreach ($row as $qr) {
            $array = array(
                "id" => $i++,
                "text" => $qr['type'],
                "value" => $qr['type'],
            );
            array_push($types, $array);
        }
        return json_encode($types);
    }//end treeQuery()


    /**
     * 获得用户信息列表
     *
     * @return mixed
     */
    public function templateQuery()
    {
        $type = input::get("type");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $result = array();
        if ($type == "type") {
            $row = Users::paginate($rows)->toArray();
        } else {
            $row = Users::where('type', $type)->paginate($rows)->toArray();
        }
        $items = [];
        foreach ($row['data'] as $value) {
            try {
                $v = Crypt::decrypt($value['pwd']);
                $value['pwd'] = $v;
            } catch (DecryptException $e) {
                //
            }
            array_push($items, $value);
        }
        $result["total"] = $row['total'];
        $result['records'] = $items;
        return json_encode($result);

    }//end templateQuery()


    /**
     * 删除用户
     *
     * @return void
     */
    public function deleteUser()
    {
        $id = input::get("id");
        $user = Users::where('id', $id)->first();
        if ($user->type == 'admin') {
            echo false;
            return;
        }
        Users::destroy($id);
        $user = $user->user;
        Template::where('user', $user)->delete();
        Template_2G::where('user', $user)->delete();
        TemplateNbi::where('user', $user)->delete();
        Kpiformula::where('user', $user)->delete();
        Kpiformula2G::where('user', $user)->delete();
        KpiformulaNbi::where('user', $user)->delete();
    }//end deleteUser()


    /**
     * 更新用户信息
     *
     * @return mixed
     */
    public function updateUser()
    {
        $id = input::get("id");
        $userName = input::get("userName");
        $name = input::get("name");
        $password = input::get("password");
        $type = input::get("type");
        $email = input::get("email");
        $province = input::get("province");
        $operator = input::get("operator");
        $unaudited = input::get("unaudited");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $res = '';
        if ($id) {
            $res = Users::where('id', $id)->update(
            [
                'user' => $userName,
                'name' => $name,
                'pwd' => Crypt::encrypt($password),
                'type' => $type,
                'email' => $email,
                'province' => $province,
                'operator' => $operator
            ]
            );
        } else {
            $newUser = new Users;
            $newUser->user = $userName;
            $newUser->name = $name;
            $newUser->pwd = Crypt::encrypt($password);
            $newUser->type = $type;
            $newUser->email = $email;
            $newUser->province = $province;
            $newUser->operator = $operator;
            $res = $newUser->save();
        }

        if ($res == 1 && $unaudited == true) {
            $this->send($email, $password);
        }

        return $res;

    }//end updateUser()

    /**
     * 发送邮件
     *
     * @param string $email 邮箱信息
     *
     * @param string $password 密码信息
     *
     * @return void
     */
    public function send($email, $password)
    {
        Mail::send('email.email', ['email' => $email, 'password' => $password], function ($message) use ($email) 
        {
            $message->to($email)->subject('Genius平台注册信息');
        }
        );
    }//end getType()

    /**
     * 获得用户组列表
     *
     * @return array
     */
    public function getType()
    {
        $row = UserGroup::get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            if ($qr['type'] != 'kpionly' && $qr['type'] != 'unaudited') {
                $items[$qr['type']] = $qr['type'];
            }
        }
        return $items;
    }//end updateUserType()

    /**
     * 更新用户组
     *
     * @return string
     */
    public function updateUserType()
    {
        $userType = input::get("userType");
        $newUsersGroup = new UserGroup;
        $newUsersGroup->type = $userType;
        $res = $newUsersGroup->save();
        return strval($res);
    }//end deleteUserType()

    /**
     * 删除用户组
     *
     * @return string
     */
    public function deleteUserType()
    {
        $type = input::get("type");
        $res = UserGroup::where('type', $type)->delete();
        Users::where('type', $type)->delete();
        return strval($res);
    }//end getMenuList()

    /**
     * 获得菜单列表
     *
     * @return mixed
     */
    public function getMenuList()
    {
        $type = input::get("type");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $items = array();
        $items['text'] = "全部菜单";
        $items['nodes'] = array();
        $items_checked = true;
        $items['state'] = array("checked" => false);

        $menu_ids = UserGroup::where('type', $type)->first()->menu_id;
        $menu_ids = explode(",", $menu_ids);
        $first_parents = MenuList::query()->selectRaw('DISTINCT first_parent')
            ->get()->toArray();
        foreach ($first_parents as $first_parent) {
            $first = array();
            $f_parent = $first_parent['first_parent'];
            $first['text'] = $f_parent;
            $first['nodes'] = array();
            $first_checked = true;
            $first['state'] = array("checked" => false);
            $second_parents = MenuList::query()->selectRaw('DISTINCT second_parent')
                ->where('first_parent', $f_parent)->get()->toArray();
            foreach ($second_parents as $second_parent) {
                $second = array();
                $s_parent = $second_parent['second_parent'];
                $second['text'] = $s_parent;
                $second['nodes'] = array();
                $second_checked = true;
                $second['state'] = array("checked" => false);
                $menus = MenuList::where('first_parent', $f_parent)->where('second_parent', $s_parent)
                    ->get()->toArray();
                foreach ($menus as $menu) {
                    $menuList = array();
                    $menuList['id'] = intval($menu['id']);
                    $menuList['text'] = $menu['menu_chinese'];
                    $menuList['value'] = $menu['menu'];
                    $flag = false;
                    foreach ($menu_ids as $menu_id) {
                        if ($menu_id == $menu['id']) {
                            $flag = true;
                            break;
                        }
                    }
                    if ($flag) {
                        $menuList['state'] = array("checked" => true);
                    } else {
                        $menuList['state'] = array("checked" => false);
                        $second_checked = false;
                        $first_checked = false;
                        $items_checked = false;
                    }
                    array_push($second['nodes'], $menuList);
                }//end foreach
                if ($second_checked) {
                    $second['state'] = array("checked" => true);
                }
                array_push($first['nodes'], $second);
            }
            if ($first_checked) {
                $first['state'] = array("checked" => true);
            }
            array_push($items['nodes'], $first);
        }
        if ($items_checked) {
            $items['state'] = array("checked" => true);
        }
        return json_encode($items);
    }//end updatePermission()

    /**
     * 更新组别
     *
     * @return string
     */
    public function updatePermission()
    {
        $type = input::get("type");
        $menuStr = input::get("menus");
        $res = UserGroup::where('type', $type)->update(['menu_id' => $menuStr]);
        return strval($res);
    }//end send()
}//end class
