<?php

namespace App\Console\Commands;

use App\Jobs\NewSitesChecker\NewSitesDiscovery;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\SiteCheck\SiteStatus;
use App\Models\SiteCheck\ManagedElement;

use Config;
use App\Jobs\NewSitesChecker\SiteCheck as siteckeck;
class SiteCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitecheck:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新建站参数检查';


    private $formatter = 'ymd';

    private $prefix = 'kget';

    private $path = 'database.connections.kget.database';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
     public function handle()
    {
    	$nodes = array();
        $row   = SiteStatus::select("enbId")->distinct()->get()->toArray();
        foreach ($row as $key => $value) {
        	$nodes[] = $value['enbId'];
        }
        date_default_timezone_set('PRC');
        // print_r(date("Y-m-d H"));exit;
        $NewSitesDiscovery = new NewSitesDiscovery();
        // print_r($nodes);exit;
         $nodes = array_merge($nodes,$NewSitesDiscovery->discovery(config('sitecheck.days')));

        foreach ($nodes as $node) {
            siteckeck::dositecheck($node);
        }
    }
}
