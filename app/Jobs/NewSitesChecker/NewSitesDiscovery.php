<?php
/**
 * Created by PhpStorm.
 * User: ericsson
 * Date: 2018/4/24
 * Time: 11:08
 */

namespace App\Jobs\NewSitesChecker;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Models\SiteCheck\SiteStatus;
use App\Models\SiteCheck\ManagedElement;
use App\Models\Mongs\Task;
use App\Models\Mongs\Databaseconns;
use DateInterval;
use DateTime;
use PDO;
use Illuminate\Support\Collection;


class NewSitesDiscovery
{
    private $formatter = 'ymd';

    private $prefix = 'kget';

    private $path = 'database.connections.kget.database';

    /**
     * 新入网站点自发现
     *
     * @param $days integer 天数
     * @return array site list.
     */
    public function discovery($days)
    {
        $e_sites = $this->fromEniq($days);
        $k_sites = $this->fromKget($days);
        
        $sites = array_unique(array_merge($e_sites,$k_sites));

        return $sites?$sites:array();
    }

    /**
     * ENIQ发现
     *
     * @param $days int 天数
     *
     * @return Collection
     */
    private  function fromEniq($days)
    {
        $day = date('Y-m-d',strtotime("-1 day"));
        $city = Databaseconns::select('connName','host','port','dbName','userName','password','subNetwork','subNetworkFdd','subNetworkNbiot')->where("cityChinese","苏州")->get()->toArray();
        // print_r($city);
        // exit;
        $dbc = new DataBaseConnection();
        $str = $dbc->getSubNets("苏州");

        // print_r($str);exit;
        $site_array= array();
        foreach($city as $value){
            $link = "dblib:host=".$value['host'].":".$value['port'].";dbname=".$value['dbName'];

            try {

                $db = new PDO($link,$value['userName'],$value['password']);

            } catch (Exception $e) {
                throw new Exception("Error Processing Request", 1);

            }
            $sql_TDD = "select distinct(substring(substring(SN,charindex(',',substring(SN,32,25))+32),11,25)) as site from dc.DC_E_ERBS_EUTRANCELLTDD_day where date_id ='".$day."' and substring(SN,charindex ('=', substring(SN, 32, 25)) + 32,charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1) in ($str)";
            $sql_FDD = "select distinct(substring(substring(SN,charindex(',',substring(SN,32,25))+32),11,25)) as site from dc.dc.DC_E_ERBS_EUTRANCELLFDD_day where date_id ='".$day."' and substring(SN,charindex ('=', substring(SN, 32, 25)) + 32,charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1) in ($str)";
            $sql_Nbiot = "select distinct(substring(substring(SN,charindex(',',substring(SN,32,25))+32),11,25)) as site from dc.DC_E_ERBS_NBIOTCELL_day where date_id ='".$day."' and substring(SN,charindex ('=', substring(SN, 32, 25)) + 32,charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1) in ($str)";

            $sites = array();
            $row1 = $db->query($sql_TDD)->fetchall(PDO::FETCH_ASSOC);
            foreach ($row1 as $key => $value) {
                $sites[] = $value['site'];

            }
          
            $row2 = $db->query($sql_FDD)->fetchall(PDO::FETCH_ASSOC);
            foreach ($row2 as $key => $value) {
                $sites[] = $value['site'];

            }
            $row3 = $db->query($sql_Nbiot)->fetchall(PDO::FETCH_ASSOC);
            foreach ($row3 as $key => $value) {
                $sites[] = $value['site'];

            }
            for($i=0;$i<$days;$i++){
                $day = date('Y-m-d',strtotime("$day -1 day"));

                $sql_TDD = "select distinct(substring(substring(SN,charindex(',',substring(SN,32,25))+32),11,25)) as site from dc.DC_E_ERBS_EUTRANCELLTDD_day where date_id ='".$day."' and substring(SN,charindex ('=', substring(SN, 32, 25)) + 32,charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1) in ($str)";
                $sql_FDD = "select distinct(substring(substring(SN,charindex(',',substring(SN,32,25))+32),11,25)) as site from dc.dc.DC_E_ERBS_EUTRANCELLFDD_day where date_id ='".$day."' and substring(SN,charindex ('=', substring(SN, 32, 25)) + 32,charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1) in ($str)";
                $sql_Nbiot = "select distinct(substring(substring(SN,charindex(',',substring(SN,32,25))+32),11,25)) as site from dc.DC_E_ERBS_NBIOTCELL_day where date_id ='".$day."' and substring(SN,charindex ('=', substring(SN, 32, 25)) + 32,charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1) in ($str)";

                $row1 = $db->query($sql_TDD)->fetchall(PDO::FETCH_ASSOC);
                $row2 = $db->query($sql_FDD)->fetchall(PDO::FETCH_ASSOC);
                $row3 = $db->query($sql_Nbiot)->fetchall(PDO::FETCH_ASSOC);

                $site_b = array();
                foreach ($row1 as $key => $value) {
                    $site_b[] = $value['site'];

                }
                foreach ($row2 as $key => $value) {
                    $site_b[] = $value['site'];

                }
                foreach ($row3 as $key => $value) {
                    $site_b[] = $value['site'];

                }
                $sites = array_diff($sites, $site_b);
                // $site_array = array();
                foreach ($sites as $key => $value) {
                    $site_array[] = $value;
                }
            }


        }
        return $site_array?array_unique($site_array):array();

    }

    /**
     * kget发现
     *
     * @param $days integer 天数
     *
     * @return Collection $sites
     */
    public function fromKget($days)
    {
        // 获得当前最新kget
        $current = Task::where('type', 'parameter')->where('taskName', 'not like', 'kgetpart%')->orderBy('startTime', 'desc')->get(['taskName'])
            ->first();
        // 获得当前站点集
        config(["database.connections.kget.database" => $current->taskName]);
        // print_r($current);exit;
        // $sites = ManagedElement::select('meContext')->get()->toArray();
        $row = ManagedElement::get(['meContext'])->toArray();
        // print_r($sites->meContext);
        $sites  = array();
        foreach ($row as $key => $value) {
            $sites[] =$value['meContext'];
        }
        // 获得最新kget日期
        list($day) = sscanf($current->taskName, 'kget%d');
        
        $date = DateTime::createFromFormat($this->formatter,$day);
   
        for ($i = 1; $i <= $days; $i++) {
            $day_b = $date->sub(new DateInterval('P1D'))->format($this->formatter);
            config(["database.connections.kget.database" => 'kget'.$day_b]);
            $row_b = ManagedElement::get(['meContext'])->toArray();
            $sites_b = array();
            foreach ($row_b as $key => $value) {
                $sites_b[] = $value['meContext'];
            }
            $sites = array_diff($sites, $sites_b);

        }

        return $sites?$sites:array();
    }
}