<?php

namespace App\Jobs\NewSitesChecker;

use App\Jobs\Job;
use App\Models\SiteStatus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteCheck extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    use KPIChecker, ParameterChecker, MrrChecker;

    /**
     * 检查指定站点状态
     *
     * @param $enbId string 站点ID
     */
    public static function doSiteCheck($enbId)
    {   

        self::doMrrCheck($enbId);
        self::doParameterCheck($enbId);
        // exit;
        self::doKPICheck($enbId);
    }
}
