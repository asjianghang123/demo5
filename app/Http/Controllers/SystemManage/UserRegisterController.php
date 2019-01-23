<?php

/**
 * UserRegisterController.php
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
use App\Models\Mongs\Users;
use Validator;
use Illuminate\Support\Facades\Crypt;

/**
 * 用户注册
 * Class UserRegisterController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class UserRegisterController extends Controller
{

    /**
     * 用户注册
     *
     * @return void
     */
    public function userRegister(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email'=>'required|max:255|email|unique:users,email',
            'password'=>'required|min:6|max:16|regex:[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,16}$]'
            
        ]);
        
        $name  = input::get("name");
        $email = input::get("email");
        $pwd   = input::get("password");

        $user = new Users;
        $user->name = $name;
        $user->pwd = Crypt::encrypt($pwd);
        $user->type = 'unaudited';
        $user->email = $email;
        $res = $user->save();
        // $result['result'] = 'success';
        // return json_encode($result);
        return redirect('/');

    }//end userRegister()


}//end class
