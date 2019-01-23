<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class User extends Authenticatable
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'pwd', 'type', 'email', 'user'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pwd', 'remember_token',
    ];

    /**
     * Create a cryped password.
     * @return mixed
     */
    public function getAuthPassword()
    {
        //用来保证老用户的密码不会进行解密，直接使用
        if (strlen($this->pwd)>20) {
            try {
                $this->pwd = Crypt::decrypt($this->pwd);
            } catch (DecryptException $e) {

            }
        }
        
        return app()['hash']->make($this->pwd);
    }
}
