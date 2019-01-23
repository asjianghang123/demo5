<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Mongs\UserGroup;
use App\Models\Mongs\MenuList;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        //和url相关的权限
        $menus = MenuList::all(['menu']);
        foreach($menus as $menu) {
            $gate->define($menu->menu, function ($url,$userType,$url2) {
                if ($userType === 'admin') {
                    return true;
                }
                $menuStr = UserGroup::query()->where('type', $userType)->first()->menu_id;
                if ($menuStr == null || $menuStr == "") {
                    return false;
                }
                $builder =  MenuList::query()->getQuery()->whereIn('id', explode(',', $menuStr))->where('menu', $url2);
                return $builder->exists();

            });
    	}
        //adminOnly权限
        $gate->define('adminOnly', function ($url, $userType) {
            if ($userType === 'admin') {
                return true;
            } else {
                return false;
            }
        });
        //特色功能
        $gate->define('features', function ($url, $userType, $province, $operator) {
            if ($userType === 'admin') {
                return true;
            }
            if ($province == "江苏" && $operator == "移动") {
                return true;
            } else {
                return false;
            }
        });
    }
}
