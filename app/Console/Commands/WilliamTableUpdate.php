<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use Illuminate\Support\Facades\DB;
use App\Models\William\Test;
use App\Models\William\LTE_Site;
use App\Models\William\GSM_Site;
use App\Models\Mongs\SiteLte;
use App\Models\Mongs\SiteGsm;
use App\Models\Kget\GSM_SERIAL_INFO;
use Config;
use App\Models\Mongs\Databaseconns;
use PDO;
use App\Models\William\LTE_Ho;
use App\Models\William\LTE_Mro;
use App\Models\MR\MroServeNeigh_day;

class WilliamTableUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'williamTableUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update william database';

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
        LTE_Site::truncate();
        $res = SiteLte::select('ecgi', 'cellName', 'siteName', 'longitude', 'latitude', 'dir', 'cellType', 'earfcn', 'pci', 'rsi', 'duplexMode')->get()->toArray();
        foreach ($res as $key => $value) {
            $lteSite = new LTE_Site();
            $lteSite->ECGI = $value['ecgi'];
            $lteSite->ECELL = $value['cellName'];
            $lteSite->SITE = $value['siteName'];
            $lteSite->Longitude = $value['longitude'];
            $lteSite->Latitude = $value['latitude'];
            $lteSite->dir = $value['dir'];
            $lteSite->cellType = $value['cellType'];
            $lteSite->EARFCN = $value['earfcn'];
            $lteSite->PCI = $value['pci'];
            $lteSite->RSI = $value['rsi'];
            $lteSite->duplexMode = $value['duplexMode'];
            $lteSite->save();
        }
        
        GSM_Site::truncate();
        // $kget = 'kget'.date('ymd', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
        $kget = 'kget'.date('ymd', strtotime('-1 day'));
        Config::set("database.connections.kget.database", $kget);
        $res = SiteGsm::select('CellIdentity', 'CELL', 'Longitude', 'Latitude', 'dir', 'cellType', 'BCCH', 'NCC', 'BCC')->get()->toArray();
        foreach ($res as $key => $value) {
            $gsmSite = new GSM_Site();
            $gsmSite->CGI = $value['CellIdentity'];
            $gsmSite->CELL = $value['CELL'];
            $gsmSite->Longitude = $value['Longitude'];
            $gsmSite->Latitude = $value['Latitude'];
            $gsmSite->dir = $value['dir'];
            $gsmSite->cellType = $value['cellType'];
            $gsmSite->BCCHNO = $value['BCCH'];
            $gsmSite->BSIC = 8*$value['NCC']+$value['BCC'];
            $gsmSite->SITE = GSM_SERIAL_INFO::select('BTS')->first()->toArray()['BTS'];
            $gsmSite->save();
        }

        LTE_Ho::truncate();
        $res = Databaseconns::select('connName', 'host', 'port', 'dbName', 'userName', 'password', 'subNetwork')->get()->toArray();
        $aWeekAgo = date("Y-m-d",strtotime("-8days",strtotime(date('Y-m-d H:i:s',time()))));
        $yestoday = date("Y-m-d",strtotime("-1days",strtotime(date('Y-m-d H:i:s',time()))));
        foreach ($res as $key => $value) {
            $pmDbDSN = "dblib:host=".$value['host'].":".$value['port'].";".((float)phpversion()>7.0?'dbName':'dbname')."=".$value['dbName'];
            $pmDB = new PDO($pmDbDSN, $value['userName'], $value['password']);
            // $subNetwork = "'".str_replace(',', "','", $value['subNetwork'])."'";
            $sql = "SELECT
                        AGG_TABLE0.EutranCellTDD,
                        AGG_TABLE0.EutranCellRelation,
                        cast(
                            pmHoExeSuccLteIntraF + pmHoExeSuccLteInterF AS DECIMAL (18, 0)
                        ) AS kpi0
                    FROM
                        (
                            SELECT
                                EutranCellTDD,
                                EutranCellRelation,
                                sum(pmHoExeSuccLteIntraF) AS 'pmHoExeSuccLteIntraF',
                                sum(pmHoExeSuccLteInterF) AS 'pmHoExeSuccLteInterF'
                            FROM
                                dc.DC_E_ERBS_EUTRANCELLRELATION_raw
                            WHERE
                                date_id >= '$aWeekAgo'
                            AND date_id <= '$yestoday'
                            GROUP BY
                                EutranCellTDD,
                                EutranCellRelation
                        ) AS AGG_TABLE0";
            $res = $pmDB->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            foreach ($res as $k => $v) {
                $lteHo = new LTE_Ho();
                $lteHo->EUtranCellTDD = $v['EutranCellTDD'];
                $lteHo->EUtranCellRelation = $v['EutranCellRelation'];
                $lteHo->HO_CMD_SUC = $v['kpi0'];
                $lteHo->save();
            }
            // [6635] => Array
            //     (
            //         [EutranCellTDD] =>  
            //         [EutranCellRelation] => 4600-666722-129
            //         [kpi0] => 0
            //     )

        }

        LTE_Mro::truncate();
        $mrCitys = ['MR_CZ', 'MR_NT', 'MR_WX', 'MR_SZ', 'MR_ZJ'];
        $yestoday = date("Y-m-d",strtotime("-1days",strtotime(date('Y-m-d H:i:s',time()))));
        foreach ($mrCitys as $key => $value) {
            Config::set("database.connections.MR_CZ.database", $value);
            $res = MroServeNeigh_day::select('ecgi', 'ecgiNeigh_direct', 'nc_times_num')->where('dateId', $yestoday)->whereNotNull('ecgiNeigh_direct')->get()->toArray();
            foreach ($res as $k => $v) {
                $lteMro = new LTE_Mro();
                $lteMro->ECGI = $v['ecgi'];
                $lteMro->ECGINeigh_direct = $v['ecgiNeigh_direct'];
                $lteMro->nc_times_num = $v['nc_times_num'];
                $lteMro->save();
            }
        }


    }
}
