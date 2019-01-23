<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Gate;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/login');
            }
        } else {
            $userType = Auth::user()->type;
            if ($userType == 'admin') {
                return $next($request);
            }
            $url = explode("/", $request->path())[0];
            if ($url == "readAllNotice" || $url == "UserSetting" || $url == "downloadCourse" || $url == "feedBack" || $url == "csvZipDownload") {
                return $next($request);
            }
            if ($url == "LTETemplateManage") {
                $url = "LTEQuery";
            }
            if ($url == "GSMTemplateManage") {
                $url = "GSMQuery";
            }
            if ($url == "NBITemplateManage") {
                $url = "NBIQuery";
            }
            if ($url == "LTETemplateManageHW") {
                $url = "LTEQuery";
            }
            if (Gate::denies($url, [$userType, $url])) {
                return redirect()->guest('/home');
            }
        }
        return $next($request);
    }
}
