<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use Illuminate\Support\Facades\DB;
use Config;
use PDO;
use App\Http\Controllers\Common\DataBaseConnection;

class Do_ChkSiteMain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Do_ChkSiteMain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update wk_app Report_100mCoEarfcnSiteCheck & Report_60degreeSectorDirCheck';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('William', 'william');

        $dateTime = date("YmdHis");
        // $dateTime = "20180702084306";
        $date_id = date("Y-m-d");
        $dir = "/opt/gback/gtools/william/output2/";

        $cmd = "call Do_main_chkSite($dateTime)";
        $db->query($cmd);
        $this->update100mCoEarfcnSiteCheck($dateTime,$date_id,$dir);
        $this->update60degreeSectorDirCheck($dateTime,$date_id,$dir);
    }

    public function update100mCoEarfcnSiteCheck($dateTime,$date_id,$dir)
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('wk_app', 'wk_app');
        $table = "Report_100mCoEarfcnSiteCheck";
        $cmd = 'LOAD DATA INFILE "'.$dir.$table.'_'.$dateTime.'.csv" INTO TABLE '.$table.' CHARACTER SET utf8 FIELDS TERMINATED BY "," OPTIONALLY ENCLOSED BY \'"\' LINES TERMINATED BY "\r\n" IGNORE 1 LINES SET date_id = "'.$date_id.'";';
        // print_r($cmd);
        $db->query($cmd);
    }

    public function update60degreeSectorDirCheck($dateTime,$date_id,$dir)
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('wk_app', 'wk_app');
        $table = "Report_60degreeSectorDirCheck";
        $cmd = 'LOAD DATA INFILE "'.$dir.$table.'_'.$dateTime.'.csv" INTO TABLE '.$table.' CHARACTER SET utf8 FIELDS TERMINATED BY "," OPTIONALLY ENCLOSED BY \'"\' LINES TERMINATED BY "\r\n" IGNORE 1 LINES SET date_id = "'.$date_id.'";';
        // print_r($cmd);
        $db->query($cmd);
    }
}