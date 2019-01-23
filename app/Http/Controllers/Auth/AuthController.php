<?php

/**
 * AuthController.php
 *
 * @category Auth
 * @package  App\Http\Controllers\Auth
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

/**
 * Class AuthController
 *
 * @category Auth
 * @package  App\Http\Controllers\Auth
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AuthController extends Controller
{
    /**
     * 用户名字串
     * 
     * @var string $username 用户名
     */
    protected $username = 'user';
    /*
        |--------------------------------------------------------------------------
        | Registration & Login Controller
        |--------------------------------------------------------------------------
        |
        | This controller handles the registration of new users, as well as the
        | authentication of existing users. By default, this controller uses
        | a simple trait to add these behaviors. Why don't you explore it?
        |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string $redirectTo 重定向URL
     */
    protected $redirectTo = '/home';


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware($this->guestMiddleware(), ['except' => 'logout']);

    }//end __construct()


    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data 用户信息
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
             'name'     => 'required|max:255',
             'email'    => 'required|email|max:255|unique:users',
             'password' => 'required|min:6|confirmed',
            ]
        );

    }//end validator()


    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data 用户信息
     * 
     * @return User
     */
    protected function create(array $data)
    {
        return User::create(
            [
             'name'     => $data['name'],
             'email'    => $data['email'],
             'password' => bcrypt($data['password']),
            ]
        );

    }//end create()


}//end class
