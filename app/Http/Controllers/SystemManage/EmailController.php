<?php

/**
 * EmailController.php
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
use App\Models\Mongs\MailGroup;
use App\Models\Mongs\MailList;
use App\Models\Mongs\CityInfo;

/**
 * 邮箱
 * Class EmailController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class EmailController extends Controller
{


    /**
     * 获得邮箱信息TREE
     *
     * @return mixed
     */
    public function treeQuery()
    {
        $scopes = MailGroup::query()->selectRaw('distinct scope')->where('scope', '!=', 'null')->get();
        $arrScope = array();
        $items = array();
        $itArr = array();
        foreach ($scopes as $scope) {
            $scopeStr = $scope->scope;
            $roles = MailGroup::where('scope', $scopeStr)->get();
            foreach ($roles as $role) {
                array_push($arrScope, array("text" => $role->role, "id" => $role->id, "scope" => $role->scope));
            }
            $items["text"] = $scopeStr;
            $items["nodes"] = $arrScope;
            $arrScope = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }//end treeQuery()


    /**
     * 获得邮箱信息列表
     *
     * @return mixed
     */
    public function getTableData()
    {
        $scope = Input::get("scope");
        $role = Input::get("role");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $result = array();
        if ($role == null) {
            $row = MailList::where('scope', $scope)->paginate($rows)->toArray();
        } else {
            $row = MailList::where('scope', $scope)->where('role', $role)->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);
    }//end getTableData()


    /**
     * 新建邮箱
     *
     * @return mixed
     */
    public function insertDownload()
    {
        $mailAddress = Input::get("mailAddress");
        $name = Input::get("name");
        $role = Input::get("role");
        $scope = Input::get("scope");
        $city = Input::get("city");
        $id = Input::get("id");

        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');

        if ($id) {
            $res = MailList::where('id', $id)->update(
            [
                'mailAddress' => $mailAddress,
                'name' => $name,
                'role' => $role,
                'scope' => $scope,
                'city' => $city
            ]
            );
        } else {
            $mailList = new MailList;
            $mailList->mailAddress = $mailAddress;
            $mailList->name = $name;
            $mailList->role = $role;
            $mailList->scope = $scope;
            $mailList->city = $city;
            $res = $mailList->save();
        }
        return $res;

    }//end insertDownload()


    /**
     * 删除邮箱
     *
     * @return mixed
     */
    public function deleteDownload()
    {
        $id = Input::get("id");
        $res = MailList::destroy($id);
        return $res;
    }//end deleteDownload()


    /**
     * 更新邮箱类别信息
     *
     * @return mixed
     */
    public function updateScope()
    {
        $scope = Input::get("scope");
        $role = Input::get("role");

        $mailGroup = new MailGroup;
        $mailGroup->scope = $scope;
        $mailGroup->scopeName = $scope;
        $mailGroup->role = $role;
        $mailGroup->roleName = $role;
        $res = $mailGroup->save();
        return strval($res);

    }//end updateScope()


    /**
     * 获得城市列表
     *
     * @return array
     */
    public function getAllCity()
    {
        $row = CityInfo::where('type', 'kpi')->get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            $items[$qr['nameC']] = $qr['nameE'];
        }
        return $items;
    }//end getAllCity()


    /**
     * 获得邮箱类别列表
     *
     * @return array
     */
    public function getScope()
    {
        $row = MailGroup::get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            $items[$qr['scopeName']] = $qr['scope'];
        }
        return $items;
    }//end getScope()


    /**
     * 获得邮箱角色列表
     *
     * @return array
     */
    public function getRole()
    {
        $scope = Input::get("scope");
        $rows = MailGroup::where('scope', $scope)->get()->toArray();
        $items = array();
        foreach ($rows as $qr) {
            $items[$qr['roleName']] = $qr['role'];
        }
        return $items;
    }//end getRole()


    /**
     * 删除邮箱类别
     *
     * @return mixed
     */
    public function deleteScope()
    {
        $id = Input::get("id");
        $scope = Input::get("scope");
        $role = Input::get("role");

        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        if ($id == 0) {
            $res = MailGroup::where('scope', $scope)->delete();
            MailList::where('scope', $scope)->delete();
            return $res;
        } else {
            $res = MailGroup::destroy($id);
            MailList::where('scope', $scope)->where('role', $role)->delete();
            return $res;
        }
    }//end deleteScope()
}//end class
