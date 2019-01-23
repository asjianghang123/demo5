<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "*/uploadFile",
        // //
        // "paramQuery/getParamTasks",
        // "paramQuery/getParamCitys",
        // "LTEQueryHW/getParamCitys",
        // // "NBIQuery/uploadFile",
        // // "GSMQuery/uploadFile",
        // "packetLossAnalysis/getCityDate",
        // // "packetLossAnalysis/uploadFile",
        // // "FlowQuery/uploadFile",
        // "consistencyCheck/getTasks",
        // "consistencyCheck/getCities",
        // "consistencyCheck/getCityList",
        // "baselineCheck/getBaseTree",
        // "baselineCheck/getParamTasks",
        // "baselineCheck/getParamCitys",
        // "SQLQuery/getParamTasks",
        // // "LteAlarmQuery/uploadFile",
        // "*/uploadFile",
        // "relationBadHandover/getCitys",
        // "relationBadHandover/allDate",
        // "RealTimeInterference/getAllCity",
        // "RealTimeInterference/getDateTime",
        // "BoardAnalysis/getAllCity",
        // "siteManage/TreeQuery",
        // "emailManage/treeQuery",
        // "storeManage/treeQuery",
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Add this:
        // return parent::handle($request, $next);
        if($request->method() == 'POST')
        {
            // return parent::handle($request, $next);
            return parent::addCookieToResponse($request, $next($request));
        }
        
        if ($request->method() == 'GET' || $this->tokensMatch($request))
        {
            return parent::handle($request, $next);
        }
        throw new TokenMismatchException;
    }
}
