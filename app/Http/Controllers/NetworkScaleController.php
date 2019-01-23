<?php

/**
 * NetworkScaleController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Config;
use App\Models\Kget\TempSiteVersion;
use App\Models\Kget\UpgradePackage;
use App\Models\Kget\ConfigurationVersion;
use App\Models\Kget\TempSiteType;
use App\Models\Kget\TempParameterRRUAndSlaveCount;
use App\Models\Kget\TempBSCCA;
use App\Models\Kget\TempBSCCACity;
use App\Models\Kget\ENodeBFunction;
use App\Models\Kget\EUtranCellTDD;
use App\Models\AutoKPI\SysCoreTemp_city_day;
//use App\Models\NBI\EutranCellTdd_city_Quarter;
use App\Models\NBM\EutranCellTdd_city_Hour;
use App\Models\Kget\AuxPlugInUnit;
use App\Models\Kget\Slot;
use App\Models\Kget\TempTermPointToMme_S1_MMEGI_Tac;
use App\Models\TABLES;

/**
 * 网络规模概览
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NetworkScaleController extends Controller
{
    /**
     * 数据库工厂对象
     *
     * @var mixed $dbc 各地市服务器连接对象
     */
    protected $dbc;


    /**
     * 获取视图
     *
     * @return mixed
     */
    public function index()
    {
        return view('network.scale');

    }//end index()


    /**
     * 获得各版本基站类型分布
     *
     * @return string
     */
    public function getBSCversionByType()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $res          = TempSiteVersion::selectRaw('softwareVersion as category')->whereNotNull('softwareVersion')->where('softwareVersion', '!=', '')->where('softwareVersion', '!=', '!!!!')->groupBy('softwareVersion')->get();
        $categories   = array();
        $series       = array();
        $categories   = $this->getHighChartCategory($res);

        $rs           = TempSiteVersion::whereNotNull('siteType')->groupBy('siteType')->get();
        foreach ($rs as $item) {
            $siteType = $item->siteType;
            $rs       = TempSiteVersion::selectRaw('count(*) as num,softwareVersion as category')->where('siteType',
                $siteType)->whereNotNull('softwareVersion')->where('softwareVersion', '!=', '')->where('softwareVersion', '!=', '!!!!')->groupBy(['siteType','softwareVersion'])->orderBy('softwareVersion', 'asc')->get();
            $series   = $this->getHighChartSeries($rs, $siteType, $series, $categories);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getBSCversionByType()


    /**
     * 获得categories(X轴)
     *
     * @param array $rs 查询结果
     *
     * @return array
     */
    public function getHighChartCategory($rs)
    {
        $categories = array();
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }

        return $categories;

    }//end getHighChartCategory()


    /**
     * 获得HighChart时序数据
     *
     * @param array  $rs         查询结果
     * @param string $seriesKey  时序键
     * @param array  $series     值集
     * @param array  $categories Categories
     *
     * @return mixed
     */
    public function getHighChartSeries($rs, $seriesKey, $series, $categories)
    {
        foreach ($categories as $category) {
            $flag = false;
            foreach ($rs as $item) {
                if ($category == $item->category) {
                    $series[$seriesKey][] = floatval($item->num);
                    $flag = true;
                    break;
                }
            }

            if (!$flag) {
                $series[$seriesKey][] = floatval(0);
            }
        }

        return $series;

    }//end getHighChartSeries()


    /**
     * 获得各地市基站版本分布
     *
     * @return string
     */
    public function getBSCversionByCity()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        $dbc     = new DataBaseConnection();
        $dbn     = $dbc->getDB('mongs', $kget);

        $sql_category = "select left(UP_CompatibilityIndex,4) as category from UpgradePackage where UpgradePackageId in (select substring_index(currentUpgradePackage,'=',-1) from ConfigurationVersion) and left(UP_CompatibilityIndex,4) != '!!!!' and left(UP_CompatibilityIndex,4) != '' group by left(UP_CompatibilityIndex,4) order by left(UP_CompatibilityIndex,4)";
        $rs           = $dbn->query($sql_category, PDO::FETCH_OBJ);
        $rs           = $rs->fetchAll();
        $categories   = array();
        $categories   = $this->getHighChartCategory($rs);
        $res          = $dbc->getCitySubNetCategories();
        $series       = array();
        foreach ($res as $items) {
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $sql        = "select left(UP_CompatibilityIndex,4) as category,count(UP_CompatibilityIndex) as num from ((select subNetwork,substring_index(currentUpgradePackage,'=',-1)  currentUpgradePackage from ConfigurationVersion where subNetwork in(".$subNetwork.") )t left join (select UpgradePackageId,UP_CompatibilityIndex from UpgradePackage group by UpgradePackageId)t1 on t.currentUpgradePackage = t1.UpgradePackageId) where left(UP_CompatibilityIndex,4) != '!!!!' and left(UP_CompatibilityIndex,4) != '' GROUP BY left(UP_CompatibilityIndex,4)";
            $rs         = $dbn->query($sql, PDO::FETCH_OBJ);
            $rs         = $rs->fetchAll();
            $series     = $this->getHighChartSeries($rs, $city, $series, $categories);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getBSCversionByCity()


    /**
     * 各类型基站数量分布
     *
     * @return string
     */
    function getBSCSiteType()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $rs      = TempSiteType::selectRaw('count(distinct meContext) num ,siteType as type')->groupBy('siteType')->get();
        return $this->getHighChartPieData($rs);

    }//end getBSCSiteType()


    /**
     * 生成分布饼图
     *
     * @param array $result 查询结果
     *
     * @return string
     */
    public function getHighChartPieData($result)
    {
        $series   = array();
        $category = array();
        foreach ($result as $item) {
            $num           = $item->num;
            $type          = $item->type;
            $series[$type] = floatval($num);
        }

        $data['series'] = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'y'    => $value,
                                ];
        }

        return json_encode($data);

    }//end getHighChartPieData()


    /**
     * 获得各地市各类基站数目分布
     *
     * @return string
     */
    function getBSCSiteTypeCity()
    {
        $dbc  = new DataBaseConnection();
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $rs           = TempSiteType::selectRaw('DISTINCT siteType as category')->orderBy('siteType', 'asc')->get();
        $categories   = array();
        $categories   = $this->getHighChartCategory($rs);
        $res          = $dbc->getCitySubNetCategories();
        $series       = array();
        foreach ($res as $items) {
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
            $rs         = TempSiteType::selectRaw('siteType as category,count(distinct meContext) as num')->whereIn('subNetwork', $subNetworkArr)->groupBy('siteType')->get();
            $series     = $this->getHighChartSeries($rs, $city, $series, $categories);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getBSCSiteTypeCity()


    /**
     * 获得各载波类型基站数目分布
     *
     * @return string
     */
    function getBSCSlave()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        //$db      = 'kget'.$yesDate;
        // $db = "kget171220";
        $result = array();
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'TempParameterRRUAndSlaveCount')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        if ($rs) {
            $db = $rs['TABLE_SCHEMA'];
            $result['db'] = $db;
            Config::set("database.connections.kget.database", $db);
            $count = DB::select("select count(table_name) as num from information_schema.tables where table_schema='$db' and table_type='base table' AND table_name='TempParameterRRUAndSlaveCount'");
            if($count[0]->num > 0) {
                $rs      = TempParameterRRUAndSlaveCount::selectRaw('count(distinct meContext) num,carriesCount as type')->whereNotNull('carriesCount')->groupBy('carriesCount')->get();
                $result['data'] = $this->getHighChartPieData($rs);
                return $result;
            }
        }
        return json_encode('nodata');
    }//end getBSCSlave()


    /**
     * 获得各地市各类载波基站数目分布
     *
     * @return string
     */
    function getBSCSlaveCity()
    {
        $dbc  = new DataBaseConnection();
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        //$db      = 'kget'.$yesDate;
        $dbc     = new DataBaseConnection();
        //$db      = 'kget'.$yesDate;
        $result = array();
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'TempParameterRRUAndSlaveCount')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        
        if ($rs) {
            $db = $rs['TABLE_SCHEMA'];
            $result['db'] = $db;
            Config::set("database.connections.kget.database", $db);
            $dbn     = $dbc->getDB('mongs', $db);
            $count = DB::select("select count(table_name) as num from information_schema.tables where table_schema='$db' and table_type='base table' AND table_name='TempParameterRRUAndSlaveCount'");
            if ($count[0]->num > 0) {
                $rs           = TempParameterRRUAndSlaveCount::selectRaw('DISTINCT carriesCount as category')->whereNotNull('carriesCount')->orderBy('carriesCount', 'asc')->get();
                $categories   = array();
                $categories   = $this->getHighChartCategory($rs);
                $res          = $dbc->getCitySubNetCategories();
                $series       = array();
                foreach ($res as $items) {
                    $city       = $items->connName;
                    $subNetwork = $items->subNetwork;
                    $subNetwork = $dbc->reCombine($subNetwork);
                    $sql        = "select carriesCount as category,count(distinct meContext) as num from TempParameterRRUAndSlaveCount where carriesCount is not null and subNetwork in(".$subNetwork.") group by carriesCount";
                    // $rs = TempParameterRRUAndSlaveCount::selectRaw('carriesCount as category,count(distinct meContext) as num')->whereNotNull('carriesCount')->whereIn('subNetwork', $subNetwork)->groupBy('carriesCount')->get();
                    $rs         = $dbn->query($sql, PDO::FETCH_OBJ);
                    $rs         = $rs->fetchAll();
                    $series     = $this->getHighChartSeries($rs, $city, $series, $categories);
                }

                $data['category'] = $categories;
                $data['series']   = array();
                foreach ($series as $key => $value) {
                    $data['series'][] = [
                                         'name' => $key,
                                         'data' => $value,
                                        ];
                }
                $result['data'] = $data;
                return $result;
            }
        }
        return json_encode('nodata');
    }//end getBSCSlaveCity()


    /**
     * CA|非CA基站数目分布
     *
     * @return string
     */
    function getBSCCA()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        //$db      = 'kget'.$yesDate;
        $result = array();
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'tempBSCCA')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        
        if ($rs) {
            $db = $rs['TABLE_SCHEMA'];
            $result['db'] = $db;
            Config::set("database.connections.kget.database", $db);
            $rs      = TempBSCCA::get();
            $result['data'] = $this->getHighChartPieData($rs);
            return $result;
        }
       
        return json_encode('nodata');

    }//end getBSCCA()


    /**
     * 各地市CA|非CA基站数目分布
     *
     * @return string
     */
    function getBSCCACity()
    {
        $dbc  = new DataBaseConnection();
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate    = $date->format('ymd');
        //$db         = 'kget'.$yesDate;
        $result = array();
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'tempBSCCA')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        
        if ($rs) {
            $db = $rs['TABLE_SCHEMA'];
            $result['db'] = $db;
            Config::set("database.connections.kget.database", $db);
            $categories = array();
            array_push($categories, "CA");
            array_push($categories, "非CA");
            $res    = $dbc->getCitySubNetCategories();
            $series = array();
            foreach ($res as $items) {
                $city   = $items->connName;
                $rs     = TempBSCCACity::selectRaw('num,type as category')->where('city', $city)->get();
                $series = $this->getHighChartSeries($rs, $city, $series, $categories);
            }

            $data['category'] = $categories;
            $data['series']   = array();
            foreach ($series as $key => $value) {
                $data['series'][] = [
                                     'name' => $key,
                                     'data' => $value,
                                    ];
            }
            $result['data'] = $data;
            return $result;
        }
       
        return json_encode('nodata');

    }//end getBSCCACity()


    /**
     * 获得基站总数
     *
     * @return \PDOStatement|string
     */
    public function getMeContextNum()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $rs      = ENodeBFunction::distinct('meContext')->get(['meContext'])->count();
        return $rs;

    }//end getMeContextNum()


    /**
     * 获得小区总数
     *
     * @return \PDOStatement|string
     */
    public function getCellNum()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $rs      = EUtranCellTDD::distinct('EUtranCellTDDId')->get(['EUtranCellTDDId'])->count();
        return $rs;

    }//end getCellNum()


    /**
     * 获得载频总数
     *
     * @return \PDOStatement|string
     */
    public function getSlaveNum()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        //$db      = 'kget'.$yesDate;
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'TempParameterRRUAndSlaveCount')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        $db = $rs['TABLE_SCHEMA'];
        Config::set("database.connections.kget.database", $db);
        $rs      = TempParameterRRUAndSlaveCount::sum('sectorCarrierRef');
        return $rs;

    }//end getSlaveNum()


    /**
     * 获得各地市基站总数
     *
     * @return \PDOStatement|string
     */
    public function getMeContextNumByCity()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $city       = Input::get('city');
        $city       = mb_substr($city, 0, 2, 'UTF-8');
        $dbc        = new DataBaseConnection();
        $subNetwork = $dbc->getSubNets($city);
        $subNetwork = str_replace("'", "", $subNetwork);
        $subNetworkArr = explode(",", $subNetwork);
        $rs  = ENodeBFunction::distinct('meContext')->whereIn('subNetwork', $subNetworkArr)->get(['meContext'])->count();
        return $rs;

    }//end getMeContextNumByCity()


    /**
     * 获得各地市小区数目总数
     *
     * @return \PDOStatement|string
     */
    public function getCellNumByCity()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $city       = Input::get('city');
        $city       = mb_substr($city, 0, 2, 'UTF-8');
        $dbc        = new DataBaseConnection();
        $subNetwork = $dbc->getSubNets($city);
        $subNetwork = str_replace("'", "", $subNetwork);
        $subNetworkArr = explode(",", $subNetwork);
        $rs = EUtranCellTDD::distinct('EUtranCellTDDId')->whereIn('subNetwork', $subNetworkArr)->get(['EUtranCellTDDId'])->count();
        return $rs;

    }//end getCellNumByCity()


    /**
     * 获得各地市载频总数
     *
     * @return \PDOStatement|string
     */
    public function getSlaveNumByCity()
    {
        $db = new DataBaseConnection();
        $kget = $db->getKgetTime();
        Config::set("database.connections.kget.database", $kget);
        $city       = Input::get('city');
        $city       = mb_substr($city, 0, 2, 'UTF-8');
        $dbc        = new DataBaseConnection();
        $subNetwork = $dbc->getSubNets($city);
        $subNetwork = str_replace("'", "", $subNetwork);
        $subNetworkArr = explode(",", $subNetwork);
        $rs    = TempParameterRRUAndSlaveCount::whereIn('subNetwork', $subNetworkArr)->sum('sectorCarrierRef')->get();
        if ($rs) {
            return $rs;
        } else {
            return '0';
        }

    }//end getSlaveNumByCity()


    /**
     * 获得各地市载频分布
     *
     * @return string
     */
    public function getRRUAndSlaveByCity()
    {
        $dbc  = new DataBaseConnection();
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        //$db      = 'kget'.$yesDate;
        $result = array();
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'TempParameterRRUAndSlaveCount')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        
        if ($rs) {
            $db = $rs['TABLE_SCHEMA'];
            $result['db'] = $db;
            Config::set("database.connections.kget.database", $db);
            $count = DB::select("select count(table_name) as num from information_schema.tables where table_schema='$db' and table_type='base table' AND table_name='TempParameterRRUAndSlaveCount'");
            if($count[0]->num > 0) {
                $rs = TempParameterRRUAndSlaveCount::selectRaw('band as category')->whereNotNull('band')->groupBy('band')->get();
                $categories   = array();
                $categories   = $this->getHighChartCategory($rs);
                $res          = $dbc->getCitySubNetCategories();
                $series       = array();
                foreach ($res as $items) {
                    $city       = $items->connName;
                    $subNetwork = $items->subNetwork;
                    $subNetwork = $dbc->reCombine($subNetwork);
                    $subNetwork = str_replace("'", "", $subNetwork);
                    $subNetworkArr = explode(",", $subNetwork);
                    $rs         = TempParameterRRUAndSlaveCount::selectRaw('band as category, sum(sectorCarrierRef) num')->whereIn('subNetwork', $subNetworkArr)->whereNotNull('band')->groupBy('band')->get();
                    $series     = $this->getHighChartSeries($rs, $city, $series, $categories);
                }

                $data['category'] = $categories;
                $data['series']   = array();
                foreach ($series as $key => $value) {
                    $data['series'][] = ['name' => $key,
                                         'data' => $value,];
                }
                $result['data'] = $data;
                return $result;
            }

        }
       
        return json_encode("nodata");
        

    }//end getRRUAndSlaveByCity()


    /**
     * 获得各频点载频分布
     *
     * @return string
     */
    public function getRRUAndSlaveBySlave()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        //$db      = 'kget'.$yesDate;

        $result = array();
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'TempParameterRRUAndSlaveCount')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        
        if ($rs) {
            $db = $rs['TABLE_SCHEMA'];
            $result['db'] = $db;
            Config::set("database.connections.kget.database", $db);
            $count = DB::select("select count(table_name) as num from information_schema.tables where table_schema='$db' and table_type='base table' AND table_name='TempParameterRRUAndSlaveCount'");
            if($count[0]->num > 0) {
                $rs  = TempParameterRRUAndSlaveCount::selectRaw('band as category, sum(sectorCarrierRef) num')->whereNotNull('band')->groupBy('band')->get();

                $categories = array();
                $series     = array();
                foreach ($rs as $item) {
                    $category     = $item->category;
                    $num          = $item->num;
                    $categories[] = $category;
                    $series[''][] = floatval($num);
                }

                $data['category'] = $categories;
                $data['series']   = array();
                foreach ($series as $key => $value) {
                    $data['series'][] = [
                                         'name' => $key,
                                         'data' => $value,
                                        ];
                }
                $result['data'] = $data;
                return $result;
            }
        }
        return json_encode("nodata");

    }//end getRRUAndSlaveBySlave()


    /**
     * 获得最大用户数，上行业务量，下行业务量，CSFB次数
     *
     * @return string
     */
    public function getNumOnAutoKPI()
    {
        $date   = new DateTime();
        $date   = $date->sub(new DateInterval('P1D'));
        $day_id = $date->format('Y-m-d');
        $result = SysCoreTemp_city_day::selectRaw('sum(`最大RRC连接用户数`) maxUser, sum(`空口上行业务量GB`) upTraffic, sum(`空口下行业务量GB`) downTraffic, sum(`语音回落到GSM次数`) csfbCount')->where('day_id', $day_id)->get()->toArray();
        if ($result[0]) {
            return json_encode($result[0]);
        } else {
            $result[0]['maxUser']     = 0;
            $result[0]['upTraffic']   = 0;
            $result[0]['downTraffic'] = 0;
            $result[0]['csfbCount']   = 0;
            return json_encode($result[0]);
        }

    }//end getNumOnAutoKPI()


    /**
     * 获得各地市最大用户数，上行业务量，下行业务量，CSFB次数
     *
     * @return string
     */
    public function getNumOnAutoKPIByCity()
    {
        $dbc    = new DataBaseConnection();
        $date   = new DateTime();
        $date   = $date->sub(new DateInterval('P1D'));
        $day_id = $date->format('Y-m-d');
        $city   = Input::get('city');
        $city   = mb_substr($city, 0, 2, 'UTF-8');
        $rs     = $dbc->getCityByCityChinese($city);
        if ($rs) {
            $city   = $rs[0]->connName;
            $result = SysCoreTemp_city_day::selectRaw('sum(`最大RRC连接用户数`) maxUser, sum(`空口上行业务量GB`) upTraffic, sum(`空口下行业务量GB`) downTraffic, sum(`语音回落到GSM次数`) csfbCount')->where('day_id', $day_id)->where('city', $city)->get()->toArray();
            if ($result) {
                return json_encode($result[0]);
            } else {
                $result[0]['maxUser']     = 0;
                $result[0]['upTraffic']   = 0;
                $result[0]['downTraffic'] = 0;
                $result[0]['csfbCount']   = 0;
                return json_encode($result[0]);
            }
        } else {
            $result[0]['maxUser']     = 0;
            $result[0]['upTraffic']   = 0;
            $result[0]['downTraffic'] = 0;
            $result[0]['csfbCount']   = 0;
            return json_encode($result[0]);
        }

    }//end getNumOnAutoKPIByCity()


    /**
     * 获得全省Vlote呼叫总数
     *
     * @return \PDOStatement|string
     */
    public function getVolteCalls()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $day_id = $date->format('Y-m-d');
        //$rs     = EutranCellTdd_city_Quarter::where('DateId', $day_id)->groupBy('DateId')->sum('ERAB_NbrAttEstab_1');
        $rs     = EutranCellTdd_city_Hour::where('date_id', $day_id)->sum('ERAB_NbrAttEstab_1');
        if ($rs) {
            return $rs;
        } else {
            return 0;
        }

    }//end getVolteCalls()


    /**
     * 获得各地市Vlote呼叫总数
     *
     * @return \PDOStatement|string
     */
    public function getVolteCallsByCity()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $day_id   = $date->format('Y-m-d');
        $dbc      = new DataBaseConnection();
        $city     = Input::get('city');
        $city     = mb_substr($city, 0, 2, 'UTF-8');
        $chengshi = $dbc->getNbiOptions($city);
        $rs       = EutranCellTdd_city_Quarter::where('DateId', $day_id)->where('City', $chengshi)->groupBy(['DateId','City'])->sum('ERAB_NbrAttEstab_1');
        if ($rs) {
            return $rs->get();
        } else {
            return '0';
        }

    }//end getVolteCallsByCity()


    /**
     * 获得地市着色信息
     *
     * @return array
     */
    public function getCitysColor()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getColors();

    }//end getCitysColor()


    /**
     * 获得各地市RRU类型，数量分布
     *
     * @return string
     */
    public function getRRUUnitTypeCity()
    {
        $dbc  = new DataBaseConnection();
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate    = $date->format('ymd');
        $db         = 'kget'.$yesDate;
        Config::set("database.connections.kget.database", $db);
        $categories = [
                       'RRU',
                       'IRU',
                       'RU',
                      ];
        $res        = $dbc->getCitySubNetCategories();
        $series     = array();
        foreach ($res as $items) {
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
            $rs         = AuxPlugInUnit::selectRaw('unitType as category, count(unitType) num')->whereIn('subNetwork', $subNetworkArr)->whereNotNull('unitType')->groupBy('unitType')->get();
            $series     = $this->getHighChartSeries($rs, $city, $series, $categories);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getRRUUnitTypeCity()


    /**
     * 获得各地市DU数量分布
     *
     * @return string
     */
    public function getDUProductData()
    {
        $dbc  = new DataBaseConnection();
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db      = 'kget'.$yesDate;
        Config::set("database.connections.kget.database", $db);
        $rs           = Slot::selectRaw('DISTINCT productData_productName as category')->where('productData_productName', '!=', '')->get();
        $categories   = array();
        $categories   = $this->getHighChartCategory($rs);
        $res          = $dbc->getCitySubNetCategories();
        $series       = array();
        foreach ($res as $items) {
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
            $rs         = Slot::selectRaw('productData_productName as category, count(productData_productName) num')->whereIn('subNetwork', $subNetworkArr)->where('productData_productName', '!=', '')->groupBy('productData_productName')->get();
            $series     = $this->getHighChartSeries($rs, $city, $series, $categories);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getDUProductData()


    /**
     * 重拼IN语句
     *
     * @param string $subNetwork 子网字串
     *
     * @return string
     */
    protected function reCombine($subNetwork)
    {
        $subNetArr  = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()

    /**
     * 获得各地市MMEGI_TAC分布
     *
     * @return string
     */
    public function getMMEGITACData()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db      = 'kget'.$yesDate;
        Config::set("database.connections.kget.database", $db);
        $dbc     = new DataBaseConnection();
        $category = $dbc->getCityCategories();
        $series     = array();
        $categories = array();
        foreach ($category as $key => $value) {
            $city = $value->category;
            array_push($categories, $city);
            $cityCh = $dbc->getCHCity($city);
            $subNetwork = $dbc->getSubNets($cityCh);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
            $rs = TempTermPointToMme_S1_MMEGI_Tac::whereIn('subNetwork', $subNetworkArr)->count();
            $series[''][] = floatval($rs);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getMMEGITACData()

}//end class
