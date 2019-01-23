<?php

/**
 * UserSettingController.php
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
use App\Models\Mongs\Users;
use Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * 用户个人设置
 * Class UserSettingController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class UserSettingController extends Controller
{


    /**
     * 更新用户信息
     *
     * @return mixed
     */
    public function updateUser(Request $request)
    {

        $this->validate($request, [
            'nickname' => 'required|max:255',
            'email'=>'required|max:255|email'
        ]);

        $id    = input::get("id");
        $name  = input::get("nickname");
        $email = input::get("email");
        $res = Users::where('id', $id)->update(['name'=>$name,'email'=>$email]);
        // return strval($res);
        return redirect('/UserSetting');
    }//end updateUser()


    /**
     * 更新密码
     *
     * @return mixed
     */
    public function updatePassword(Request $request)
    {
        //因为有老用户存在，不能直接去数据库进行比较，只好先拿到密码，判断是否需要解密，再和输入的比较；如果没有老用户，直接把输入的进行加密，然后和数据库比较
        $pwdInDB = Users::where('id', $request->id)->first(['pwd'])->pwd;
        if (strlen($pwdInDB)>20) {
            try {
                $pwdInDB = Crypt::decrypt($pwdInDB);
            } catch (DecryptException $e) {

            }
        }
        $messages = [
            'oldPwd.in' => 'old password is wrong',
        ];
        $this->validate($request, [
            'oldPwd' => 'required|max:255|in:'.$pwdInDB,
            'newPwd'=>'required|min:6|max:16|regex:[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,16}$]',
            'confirmPwd'=>'required|same:newPwd'
        ],$messages);

        $id  = input::get("id");
        $pwd = input::get("newPwd");
        $res = Users::where('id', $id)->update(['pwd'=>Crypt::encrypt($pwd)]);
        // return strval($res);
        Auth::logout();
        return redirect('/');
    }//end updatePassword()
}//end class
