<?php

/**
 * SiteController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Cache;
use Exception;
use App\Models\Mongs\City;
use App\Models\Mongs\SiteLte;
use App\Models\Mongs\SiteGsm;
use App\Models\Mongs\SiteCdma;
use App\Models\Mongs\FtpServerInfo;
use App\Models\Mongs\MajorActivities;
use App\Models\Mongs\MajorActivities_2G;
use App\Models\Mongs\Template;
use App\Models\Mongs\Kpiformula;
use App\Models\Mongs\OtherSiteLte;
use App\Models\Mongs\NewSiteLte;
use App\Models\WK_APP\Report_LatLonDirCheck_Combined;
use App\Models\WK_APP\Report_60degreeSectorDirCheck;
use App\Models\WK_APP\Report_100mCoEarfcnSiteCheck;
use App\Models\Mongs\AbnormalStationCounts;


/**
 * 站点管理
 * Class SiteController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SiteController extends Controller
{


    /**
     * 获得城市列表
     *
     * @return mixed
     */
    public function treeQuery()
    {
        $row = City::groupBy('cityName')->get()->toArray();
        $citys = array();
        array_push($citys, array("id" => 0, "text" => "全部城市", "value" => "city"));
        foreach ($row as $qr) {
            $array = array(
                "id" => $qr["id"],
                "text" => $qr['cityNameChinese'],
                "value" => $qr['cityName'],
            );
            array_push($citys, $array);
        }
        return json_encode($citys);
    }//end TreeQuery()


    /**
     * 查询4G站点信息
     *
     * @return mixed
     */
    public function querySite4G()
    {
        $value = input::get("value");
        $searchText = input::get("searchText");
        // $table = input::get("table");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($searchText) {
            $searchText = "%".$searchText."%";
            $conn = SiteLte::where(function($query) use ($searchText) {
                $query->where('cellName', 'like', $searchText)->orWhere('ecgi', 'like', $searchText);
            });
        } else {
            $conn = new SiteLte;
        }
        if ($value == "city") {
            $row = $conn->orderBy('id', 'asc')->paginate($rows)->toArray();
        } else {
            $row = $conn->where('city', $value)->orderBy('id', 'asc')->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['rows'] = $row['data'];
        return json_encode($result);
    }//end QuerySite4G()


    /**
     * 查询2G站点信息
     *
     * @return mixed
     */
    public function querySite2G()
    {
        $value = input::get("value");
        $searchText = input::get("searchText");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($searchText) {
            $searchText = "%".$searchText."%";
            $conn = SiteGsm::where('CELL', 'like', $searchText);
        } else {
            $conn = new SiteGsm;
        }
        if ($value == "city") {
            $row = $conn->orderBy('id', 'asc')->paginate($rows)->toArray();
        } else {
            $row = $conn->where('city', $value)->orderBy('id', 'asc')->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['rows'] = $row['data'];
        return json_encode($result);

    }//end QuerySite2G()

    /**
     * 查询3G站点信息
     *
     * @return mixed
     */
    public function querySite3G()
    {
        $value = input::get("value");
        $table = input::get("table");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($value == "city") {
            $row = SiteCdma::paginate($rows)->toArray();
        } else {
            $row = SiteCdma::where('city', 'like', $value . "%")->orderBy('id', 'asc')->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);

    }//end QuerySite2G()

    /**
     * 查询友商站点信息
     *
     * @return mixed
     */
    public function querySiteOther4G(){
        $value = input::get("value");
        $searchText = input::get("searchText");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($searchText) {
            $searchText = "%".$searchText."%";
            $conn = OtherSiteLte::where(function($query) use ($searchText) {
                $query->where('cellName', 'like', $searchText)->orWhere('ecgi', 'like', $searchText);
            });
        } else {
            $conn = new OtherSiteLte;
        }
        if ($value == "city") {
            $row = $conn->orderBy('id', 'asc')->paginate($rows)->toArray();
        } else {
            $row = $conn->where('city', $value)->orderBy('id', 'asc')->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['rows'] = $row['data'];
        return json_encode($result);
    }

    /**
     * 读取CSV文件
     *
     * @return mixed
     */
    public function getFileContent()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $table = input::get("table");
        $city = input::get("city");
        $fileName = Input::get('fileName');
        $importDate = input::get("importDate");
        $importType = Input::get("importType");
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $filePath = dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/public/";
        $fileName = $filePath . "common/files/" . $fileName;
        $data = array();
        if ($table == 'siteManage') {
            if ($importType == 'replace') {
                SiteLte::where('city', 'like', $city . "%")->delete();
                $sql = "LOAD DATA INFILE '$fileName' INTO TABLE siteLte character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,`noofTxAntennas(Site)`,highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,`覆盖属性`,`是否在网`) set city = '$city',importDate = '$importDate'";
                try{
                    $query = $db->exec($sql);
                }catch(Exception $e){
                    $data['message'] = $e->getMessage();
                    return $data;
                }
                $db->query("call siteLteParamValueCheck('".$city."',@resultStr)");
                $resultStr = $db->query("select @resultStr")->fetchColumn();
                if ($resultStr != '') {

                    $data['params'] = explode(',', $resultStr);
                    return $data;

                }
                $db->query("call siteLteParameter('" . $city . "');");
            }else if ($importType == 'apend'){
                $sql = "LOAD DATA INFILE '$fileName' INTO TABLE siteLte character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,`noofTxAntennas(Site)`,highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,`覆盖属性`,`是否在网`) set city = '$city',importDate = '$importDate'";
                try{
                    $query = $db->exec($sql);
                }catch(Exception $e){
                    $data['message'] = $e->getMessage();
                    return $data;
                }
            }
            $data['sign'] = "4G";
            $items = SiteLte::select('cellName')->where('city', 'like', $city . "%")
                 ->groupBy("cellName")
                 ->havingRaw("count(cellName) > 1")->get()->toArray();
            $data['result'] = $items;
            Cache::store('file')->flush(); //清除缓存
            // 更新完siteLte后执行脚本
            system("cd /opt/lampp/htdocs/genius;php artisan GetAbnormalStationCounts");
        } else if ($table == '2GSiteManage') {
            SiteGsm::where('city', $city)->delete();

            $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE siteGsm character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(CELL,CellNameChinese,CellIdentity,BAND,ARFCN,Longitude,Latitude,dir,cellType,height,plmnIdentity_mcc,plmnIdentity_mnc,LAC,BCCH,BCC,NCC,dtmSupport) set city = '$city',importDate = '$importDate'";
            $query = $db->exec($sql);
            $data['sign'] = "2G";
        } else if ($table == 'otherSiteManage'){
            OtherSiteLte::where('city', 'like', $city . "%")->delete();

            $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE otherSiteLte character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(ecgi,cellName,cellNameChinese,siteName,siteNameChinese,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,band,channelBandWidth,`noofTxAntennas(Site)`,cluster,`覆盖属性`,`厂商`) set city = '$city',importDate = '$importDate'";
            $query = $db->exec($sql);
            $data['sign'] = "other4G";
            Cache::store('file')->flush(); //清除缓存
        } else if ($table == '3GSiteManage') {
            SiteCdma::where('city', $city)->delete();

            $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE siteCdma character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(Cell,SiteNo,Name,Area,MSC,RNC,RNCID,cid,LAC,PSC,uarfcnDl,type,`indoor/outdoor`,Longitude,Latitude,Address,Antennaaddress,3Gantennaheight,3Gantennadirection,`Federlength()`,3Gantennatype,`3GantennaH-beam`,`3GantennaV-beam`,`3GE-tiltrange`,`3GantennaE-tilt`,`3GantennaM-tilt`,dualbandantennawithGSM,dualbandantennawithDCS,`Co-siteGSMsite`,`Co-siteGSMsitename`,GSMantennaheight,GSMantennadirection,GSMantennatilt,`Co-siteDCSsite`,`Co-siteDCSsitename`,DCSantennaheight,DCSantennadirection,DCSantennatilt,city) set city = '$city' ,importDate = '$importDate'";
            $query = $db->exec($sql);
            $data['sign'] = "3G";
        }//end if
        if ($query) {
            return $data;
        } else {
            return 'false';
        }

    }//end getFileContent()

    public function getMAFileContent() {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $table = input::get("table");
        $city = input::get("city");
        $fileName = Input::get('fileName');
        $importDate = input::get("importDate");
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $fileName = "common/files/" . $fileName;
        if ($table == 'siteManage') {
            MajorActivities::where('city', $city)->delete();

            $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE majorActivities character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(city,templateName_title,templateName,templateName_checkRange,cell) set importDate = '$importDate'";
            $query = $db->exec($sql);

            $sign = "4G";
        } else if ($table == '2GSiteManage') {
            MajorActivities_2G::where('city', $city)->delete();

            $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE majorActivities_2G character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(city,templateName_title,templateName,templateName_checkRange,cell,bsc,configure) set importDate = '$importDate'";
            var_dump($sql);
            $query = $db->exec($sql);

            $sign = "2G";
        }//end if
        if ($query) {
            return $sign;
        } else {
            return 'false';
        }
    }



    /**
     * 下载站点数据
     *
     * @return array 执行结果
     */
    public function downloadFile()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $result = array();
        $table = input::get("table");
        $city = input::get("city");
        $searchText = input::get("searchText");

        $fileName = "common/files/站点信息_" . $city . "_" . date('YmdHis') . ".csv";
        if ($table == 'siteManage') {
            $column = 'ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,`noofTxAntennas(Site)`,highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,覆盖属性,是否在网,city,importDate';
            if ($searchText) {
                $searchText = "%".$searchText."%";
                $conn = SiteLte::where(function($query) use ($searchText) {
                    $query->where('cellName', 'like', $searchText)->orWhere('ecgi', 'like', $searchText);
                });
            } else {
                $conn = new SiteLte;
            }
            if ($city == "city") {
                $items = $conn->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = $conn->selectRaw($column)->where('city', $city)->orderBy('id', 'asc')->get()->toArray();
            }
        } else if ($table == '2GSiteManage') {
            $column = 'CELL,CellNameChinese,CellIdentity,BAND,ARFCN,Longitude,Latitude,dir,cellType,height,plmnIdentity_mcc,plmnIdentity_mnc,LAC,BCCH,BCC,NCC,dtmSupport,city,importDate ';
            if ($searchText) {
                $searchText = "%".$searchText."%";
                $conn = SiteGsm::where('CELL', 'like', $searchText);
            } else {
                $conn = new SiteGsm;
            }
            if ($city == "city") {
                $items = $conn->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = $conn->selectRaw($column)->where('city', $city)->orderBy('id', 'asc')->get()->toArray();
            }
        } else if ($table == 'otherSiteManage') {
            $column = 'ecgi,cellName,cellNameChinese,siteName,siteNameChinese,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,band,channelBandWidth,`noofTxAntennas(Site)`,cluster,覆盖属性,厂商,city,importDate';
            if ($searchText) {
                $searchText = "%".$searchText."%";
                $conn = OtherSiteLte::where(function($query) use ($searchText) {
                    $query->where('cellName', 'like', $searchText)->orWhere('ecgi', 'like', $searchText);
                });
            } else {
                $conn = new OtherSiteLte;
            }
            if ($city == "city") {
                $items = $conn->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = $conn->selectRaw($column)->where('city', $city)->orderBy('id', 'asc')->get()->toArray();
            }
        } else if ($table == '3GSiteManage') {
            $column = 'Cell,SiteNo,Name,Area,MSC,RNC,RNCID,cid,LAC,PSC,uarfcnDl,type,`indoor/outdoor`,Longitude,Latitude,Address,Antennaaddress,3Gantennaheight,3Gantennadirection,`Federlength()`,3Gantennatype,`3GantennaH-beam`,`3GantennaV-beam`,`3GE-tiltrange`,`3GantennaE-tilt`,`3GantennaM-tilt`,dualbandantennawithGSM,dualbandantennawithDCS,`Co-siteGSMsite`,`Co-siteGSMsitename`,GSMantennaheight,GSMantennadirection,GSMantennatilt,`Co-siteDCSsite`,`Co-siteDCSsitename`,DCSantennaheight,DCSantennadirection,DCSantennatilt,city,importDate';
            if ($city == 'city') {
                $items = SiteCdma::query()->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = SiteCdma::query()->selectRaw($column)->where('city', 'like', $city . "%")->orderBy('id', 'asc')->get()->toArray();
            }
        }
        //end if
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;

    }//end downloadFile()


    /**
     * 导出模板数据
     *
     * @return array 导出结果
     */
    public function downloadTemplateFile()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $result = array();
        $table = input::get("table");

        if ($table == 'siteManage') {
            $city = input::get("city");
            $fileName = "common/files/站点信息_" . $city . "_" . date('YmdHis') . ".csv";
            $column = 'ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,`noofTxAntennas(Site)`,highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,覆盖属性,是否在网';

            if ($city == 'city') {
                $items = SiteLte::query()->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = SiteLte::query()->selectRaw($column)->where('city', 'like', $city . "%")->orderBy('id', 'asc')->get()->toArray();
            }
        } else if ($table == '2GSiteManage') {
            $city = input::get("city");
            $fileName = "common/files/站点信息_" . $city . "_" . date('YmdHis') . ".csv";
            $column = 'CELL,CellNameChinese,CellIdentity,BAND,ARFCN,Longitude,Latitude,dir,cellType,height,plmnIdentity_mcc,plmnIdentity_mnc,LAC,BCCH,BCC,NCC,dtmSupport';

            if ($city == 'city') {
                $items = SiteGsm::query()->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = SiteGsm::query()->selectRaw($column)->where('city', 'like', $city . "%")->orderBy('id', 'asc')->get()->toArray();
            }
        } else if ($table == 'otherSiteManage'){
            $city = input::get("city");
            $fileName = "common/files/站点信息_" . $city . "_" . date('YmdHis') . ".csv";
            $column = 'ecgi,cellName,cellNameChinese,siteName,siteNameChinese,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,band,channelBandWidth,`noofTxAntennas(Site)`,cluster,覆盖属性,厂商';

            if ($city == 'city') {
                $items = OtherSiteLte::query()->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = OtherSiteLte::query()->selectRaw($column)->where('city', 'like', $city . "%")->orderBy('id', 'asc')->get()->toArray();
            }
        } else if ($table == '3GSiteManage') {
            $city = input::get("city");
            $fileName = "common/files/站点信息_" . $city . "_" . date('YmdHis') . ".csv";
            $column = 'Cell,SiteNo,Name,Area,MSC,RNC,RNCID,cid,LAC,PSC,uarfcnDl,type,`indoor/outdoor`,Longitude,Latitude,Address,Antennaaddress,3Gantennaheight,3Gantennadirection,`Federlength()`,3Gantennatype,`3GantennaH-beam`,`3GantennaV-beam`,`3GE-tiltrange`,`3GantennaE-tilt`,`3GantennaM-tilt`,dualbandantennawithGSM,dualbandantennawithDCS,`Co-siteGSMsite`,`Co-siteGSMsitename`,GSMantennaheight,GSMantennadirection,GSMantennatilt,`Co-siteDCSsite`,`Co-siteDCSsitename`,DCSantennaheight,DCSantennadirection,DCSantennatilt,city';

            if ($city == 'city') {
                $items = SiteCdma::query()->selectRaw($column)->orderBy('city', 'asc')->get()->toArray();
            } else {
                $items = SiteCdma::query()->selectRaw($column)->where('city', 'like', $city . "%")->orderBy('id', 'asc')->get()->toArray();
            }
        }//end if
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }//end downloadTemplateFile()

    /**
     * 查询IP信息
     *
     * @return mixed
     */
    public function QueryIP()
    {
        $city = input::get('city');
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($city == "city") {
            $row = FtpServerInfo::where('type', 'kget')->paginate($rows)->toArray();
        } else {
            $row = FtpServerInfo::where('city', $city)->where('type', 'kget')->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);
    }

    /**
     * 查询4G重大信息
     *
     * @return mixed
     */
    public function QueryMajorActivities()
    {
        $city = input::get('city');
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($city == "city") {
            $row = MajorActivities::query()->paginate($rows)->toArray();
        } else {
            $row = MajorActivities::where('city', $city)->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);
    }

    /**
     * 查询2G重大信息
     *
     * @return mixed
     */
    public function QueryMajorActivities_2G()
    {
        $city = input::get('city');
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($city == "city") {
            $row = MajorActivities_2G::query()->paginate($rows)->toArray();
        } else {
            $row = MajorActivities_2G::where('city', $city)->paginate($rows)->toArray();
        }
       
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);

    }


    /**
     * 上传IP信息
     *
     * @return mixed
     */
    public function uploadIPFile()
    {  
        $city = input::get("city");
        $dir = input::get("dir");

        $file = $_FILES['fileImport']['tmp_name'];
        if ($city == "suzhou") {
            $fileName = "SZ.txt";
        } else {
            $fileName = strtoupper($city).".txt";
        }
        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }

        move_uploaded_file($file, "common/files/".$fileName);

        $scp = "sudo scp -r common/files/".$fileName." ".$dir;
        // print_r($scp);
        $res = exec($scp);
        echo $res;

        // exec("rm -rf common/files/".$fileName);
    }

    /**
     * 4G重大信息文件导出
     *
     * @return mixed
     */
    public function majorActivities_export() 
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $result = array();

        $fileName = "common/files/重大活动保障信息4G_" . date('YmdHis') . ".csv";
        $column = 'city,templateName_title,templateName,templateName_checkRange,cell';
        $items = MajorActivities::query()->selectRaw($column)->orderBy('id', 'asc')->get()->toArray();
    
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }

    /**
     * 2G重大信息文件导出
     *
     * @return mixed
     */
    public function majorActivities_2G_export() 
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $result = array();

        $fileName = "common/files/重大活动保障信息2G_" . date('YmdHis') . ".csv";
        $column = 'city,templateName_title,templateName,templateName_checkRange,cell,bsc,configure';
        $items = MajorActivities_2G::query()->selectRaw($column)->orderBy('id', 'asc')->get()->toArray();
    
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }

    public function insertdata()
    {
        $templateTitle = Input::get('templateTitle');
        $templateName = Input::get('templateName');   //模板名
        $city = Input::get('city');
        $id = Input::get('id');
        $kpi = Input::get('kpi');   //指标名
        $symbol = Input::get('symbol');
        $num = Input::get('num');
        $andOr = Input::get('andOr');
        $insertdata = "";
        // print_r($templateName.$id.$kpi.$symbol.$num.$andOr);
        $data = [];
        if ($kpi == '') {
            $data['check'] = 'no';
            return;
        }
        $items = Template::selectRaw('elementId')->where('templateName', $templateName)->get()->toArray();
        $itemArr = explode(',', $items[0]['elementId']);
        $kpiCheck = Kpiformula::selectRaw('kpiName')->whereIn('id', $itemArr)->get()->toArray();
        $flag = 0;
        foreach ($kpiCheck as $value) {
            if ($kpi == $value['kpiName']) {
                $flag = 1;
            }
        }
        $data['check'] = 'yes';
        if ($flag == 0) {
            $data['check'] = 'no';
            return;
        }

        // print_r($templateTitle . $templateName. $city);

        $getData = MajorActivities::selectRaw('templateName_checkRange')
                    ->where(['templateName_title'=>$templateTitle,'templateName'=>$templateName,'city'=>$city])
                    ->get()
                    ->toArray();
        
        if ($andOr == '') {
            $insertdata = $kpi . " " . $symbol . " " . $num;
            DB::table('majorActivities')
                ->where(['templateName_title'=>$templateTitle,'templateName'=>$templateName,'city'=>$city])
                ->update(['templateName_checkRange' => $insertdata]);
        } else {
            $insertdata = $getData[0]['templateName_checkRange'] . ' ' . $andOr . $kpi . " " . $symbol . " " . $num;
            DB::table('majorActivities')
                ->where(['templateName_title'=>$templateTitle,'templateName'=>$templateName,'city'=>$city])
                ->update(['templateName_checkRange' => $insertdata]);
        }
        print_r($insertdata);

    }
    /**
     * 获取新站站点信息表头
     *
     * @return mixed
     */
    public function getNewSiteLteField(){
        $city = Input::get('city');
        //$table = Input::get('table');

        $data = array();
        $row = NewSiteLte::where("city","=",$city)->first();
        if ($row) {
            $data = array_keys($row->toArray());
        }else{
            $data['result'] = "error";
        }
        return $data;
    }// end getNewSiteLteField
    /**
     * 获取新站站点信息
     *
     * @return mixed
     */
    public function getNewSiteLte(){
        $city = Input::get("city");
        $page = Input::get("limit");
        $cellName = Input::get("searchText");
        $rows = NewSiteLte::where("city","=",$city)->where("cellName","like","%".$cellName."%")->orderBy("id","desc")->paginate($page)->toArray();
        $result         = array();
        $result["total"] = $rows['total'];
        $result['rows'] = $rows['data'];
        return json_encode($result);
    }// end getNewSiteLte
    /**
     * 获取删除指定新站站点
     *
     * @return mixed
     */
    public function deleteNewSiteLte(){
        $ids = Input::get("ids");
        $rs = NewSiteLte::whereIn("id",$ids)->delete();
        return $rs;
    }// end deleteNewSiteLte
    /**
     * 导出新站站点
     *
     * @return mixed
     */
    public function exportNewSiteLte(){
        $city = input::get("city");
        $cellName = Input::get("searchText");
        $fileName = "common/files/新站站点信息_" . $city . "_" . date('YmdHis') . ".csv";
        $column = 'ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,`noofTxAntennas(Site)`,highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,覆盖属性,是否在网';
        $items = NewSiteLte::query()->selectRaw($column)->where("city", "=", $city)->where('cellName', 'like', "%".$cellName . "%")->get()->toArray();
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }// end exportNewSiteLte
    /**
     * 获取给定地市ipList文件内容
     *
     * @return mixed
     */
    public function openIpListFile(){
        $city = Input::get("city");
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        //print_r($path);
        //return;
        $json_string = file_get_contents($path.'/public/common/json/nodeList.json');  
        //print_r("expression".$json_string);
        // 把JSON字符串转成PHP数组  
        $nodeList = json_decode($json_string, true);
        $nodeIp = $nodeList[$city]['ip'];
        $port = $nodeList[$city]['port'];
        $user = $nodeList[$city]['user'];
        $password = $nodeList[$city]['password'];
        $fileName = $nodeList[$city]['fileName'];

        $remoteFile = "/data/trace/kget/script/autokget/script/".$fileName;
        $localPath = $path."/public/common/txt/";

        $command = $path."/public/common/sh/scpIpList.sh $nodeIp $port $user $remoteFile $localPath $fileName";
        //print_r($command);
        exec($command);
        $myfile = fopen($localPath."/".$fileName, "r") or die("Unable to open file!");
        $content = "";
        while (!feof($myfile)) {
            $content .=fgets($myfile);
        }
        fclose($myfile);
        $data['content'] = $content;
        $data['IPNum'] = count(file($localPath."/".$fileName));
        return $data;
        //$command = "scp -P $port $user@$nodeIp:/data/trace/kget/script/autokget/script/WUXI.txt ".$path."/public/common/txt/";
        
    }// end openIpListFile
    /**
     * 从各个地市的docker上获取地市的ipList文件
     *
     * @return mixed
     */
    public function exportIpListFile(){
        $city = Input::get("city");
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        //print_r($path);
        //return;
        $json_string = file_get_contents($path.'/public/common/json/nodeList.json');  
        //print_r("expression".$json_string);
        // 把JSON字符串转成PHP数组  
        $nodeList = json_decode($json_string, true);
        $nodeIp = $nodeList[$city]['ip'];
        $port = $nodeList[$city]['port'];
        $user = $nodeList[$city]['user'];
        $password = $nodeList[$city]['password'];
        $fileName = $nodeList[$city]['fileName'];

        $remoteFile = "/data/trace/kget/script/autokget/script/".$fileName;
        $localPath = $path."/public/common/txt/";

        $command = $path."/public/common/sh/scpIpList.sh $nodeIp $port $user $remoteFile $localPath $fileName";
        //print_r($command);
        exec($command);
        return $fileName;
    }
    /**
     * 保存给定地市ipList文件内容
     *
     * @return mixed
     */
    public function saveIpListFile(){
        $city = Input::get("city");
        $content = Input::get("content");
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

        $json_string = file_get_contents($path.'/public/common/json/nodeList.json');  
        //print_r("expression".$json_string);
        // 把JSON字符串转成PHP数组  
        $nodeList = json_decode($json_string, true);
        $nodeIp = $nodeList[$city]['ip'];
        $port = $nodeList[$city]['port'];
        $user = $nodeList[$city]['user'];
        $password = $nodeList[$city]['password'];
        //$fileName = $nodeList[$city]['fileName'];

        $localPath = $path."/public/common/txt/";
        $TIME = $this->udate('YmdHisu');

        $fileName = "ipList_".$city."_".$TIME."_all.txt";
        $localFile = $localPath.$fileName;
        $myfile = fopen($localFile, "w") or die("Unable to open file!");
        $content = Input::get("content");
        fwrite($myfile, $content);
        fclose($myfile);
        $remotePath = "/data/ftpcfg";
        $command = $path."/public/common/sh/uploadIpList.sh $nodeIp $port $user $remotePath $localFile";
        //print_r($command);
        exec($command);
    }
    /**
     * 获取当前时间毫秒级
     *
     * @return str
     */
    public function udate($format = 'u', $utimestamp = null) {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }//end udate
    /**
     * 读取IpList文件
     *
     * @return mixed
     */
    public function getIpListFileContent()
    {   

        $city = Input::get("city");
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

        $json_string = file_get_contents($path.'/public/common/json/nodeList.json');  
        //print_r("expression".$json_string);
        // 把JSON字符串转成PHP数组  
        $nodeList = json_decode($json_string, true);
        $nodeIp = $nodeList[$city]['ip'];
        $port = $nodeList[$city]['port'];
        $user = $nodeList[$city]['user'];
        $password = $nodeList[$city]['password'];

        $localPath = $path."/public/common/txt/";
        $TIME = $this->udate('YmdHisu');

        $fileName = "ipList_".$city."_".$TIME."_all.txt";
        $localFile = $localPath.$fileName;
        $myfileW = fopen($localFile, "w") or die("Unable to open file!");
        $myfileR = fopen("common/files/".Input::get('fileName'), "r") or die("Unable to open file!");
        while (!feof($myfileR)) {
            $row = fgets($myfileR);
            fwrite($myfileW, $row);
            echo $row;
        }
        fclose($myfileR);
        fclose($myfileW);
        $remotePath = "/data/ftpcfg";
        $command = $path."/public/common/sh/uploadIpList.sh $nodeIp $port $user $remotePath $localFile";
        //print_r($command);
        exec($command);
    }//end getFileContent()

    /**
     * 异常基站站点数据数量
     *
     * @return mixed
     */
    public function getAbnormalStationCounts()
    {
        $dateId = AbnormalStationCounts::distinct()->orderBy('dateId','desc')->first(['dateId'])->toArray()['dateId'];

        $city = input::get("city");
        if ($city == 'city') {
            $result['new_station_count'] = 0;
            $result['data_loss_count'] = 0;
            $result['repaeting_site_count'] = 0;
            $result['illegal_format_count'] = 0;
            $result['cross_station_count'] = 0;
            $result['same_station_count'] = 0;
            $result['azimuth_check_count'] = 0;
            $result['lon_lat_check_count'] = 0;

            $counts = AbnormalStationCounts::where('dateId', $dateId)->get()->toArray();
            foreach ($counts as $count) {
                foreach ($count as $key => $value) {
                    if ($key == 'dateId' || $key == 'city') {
                        continue;
                    } else {
                        $result[$key] += $value;
                    }
                }
            }

            return json_encode($result);
        } else {
            $count = AbnormalStationCounts::where('dateId', $dateId)->where('city', $city)->first()->toArray();
            return json_encode($count);
        }
    }

    /**
     * 异常基站站点数据导出功能
     *
     * @return mixed
     */
    public function exportAbnormalStation()
    {
        $action = input::get("action");
        $city = input::get("city");

        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        if ($city == 'city') {
            $filter = "";
            $dimension = 'all';
        } else {
            // $conn = SiteLte::where('city', $city);
            $filter = "where city = '$city'";
            $dimension = 'city';
        }

        $fileName = "common/files/异常基站站点数据_".$action."_" . date('YmdHis') . ".csv";
        $items = [];
        $fileUtil = new FileUtil();

        switch ($action) {
            case 'new_station':
                $column = 'ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,noofTxAntennas(Site),highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,覆盖属性,是否在网';
                $items = NewSiteLte::where("city", $city)->get(explode(",", $column))->toArray();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                break;
            case 'data_loss':
                $column = "id,ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,cellType,tiltM,tiltE,antHeight,address,band,city";
                $sql = "SELECT $column FROM siteLte WHERE (ecgi IS NULL OR length(ecgi) = 0
                                                        OR ECI IS NULL OR length(ECI) = 0
                                                        OR cellName IS NULL OR length(cellName) = 0
                                                        OR cellNameChinese IS NULL OR length(cellNameChinese) = 0
                                                        OR siteName IS NULL OR length(siteName) = 0
                                                        OR siteNameChinese IS NULL OR length(siteNameChinese) = 0
                                                        OR duplexMode IS NULL OR length(duplexMode) = 0
                                                        OR rsi IS NULL OR length(rsi) = 0
                                                        OR tac IS NULL OR length(tac) = 0
                                                        OR longitude IS NULL OR length(longitude) = 0
                                                        OR latitude IS NULL OR length(latitude) = 0
                                                        OR dir IS NULL OR length(dir) = 0
                                                        OR pci IS NULL OR length(pci) = 0
                                                        OR earfcn IS NULL OR length(earfcn) = 0
                                                        OR cellType IS NULL OR length(cellType) = 0
                                                        OR tiltM IS NULL OR length(tiltM) = 0
                                                        OR tiltE IS NULL OR length(tiltE) = 0
                                                        OR antHeight IS NULL OR length(antHeight) = 0
                                                        OR address IS NULL OR length(address) = 0
                                                        OR band IS NULL OR length(band) = 0
                                                        OR city IS NULL OR length(city) = 0)";
                if ($city != 'city') {
                    $sql .= " and city = '$city'";
                }
                // $items = $conn->get(explode(",", $column))->toArray();
                $items = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                $fileUtil->resultToCSV2($column, $items, $fileName);
                break;
            case 'repaeting_site':
                $column = "id,ecgi,siteName,address,count";
                $sql = "select $column from (select *,count(*) as count from siteLte $filter group by ecgi,siteName,address)as a where count > 1 order by count desc";
                $items = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                $fileUtil->resultToCSV2($column, $items, $fileName);
                break;
            case 'illegal_format':
                $column = "id,ecgi,duplexMode,cellType,band";
                $sql = "select $column from siteLte where (ecgi not REGEXP '^([0-9]+)-([0-9]+)-([0-9]+)$'
                                                        or duplexMode not in ('TDD','FDD','NB')
                                                        or (duplexMode = 'TDD' and band not in ('D','E','F'))
                                                        or (duplexMode in ('FDD','NB') and band not in ('band3','band8'))
                                                        or cellType not in ('Outdoor-macro-sector','Outdoor-macro-omni','Outdoor-micro-sector','Outdoor-micro-omni','Indoor-IBS'))";
                if ($city != 'city') {
                    $sql .= " and city = '$city'";
                }
                $items = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                $fileUtil->resultToCSV2($column, $items, $fileName);
                break;
            case 'cross_station':
                $fileName = $this->exportFile(1, $dimension, $city ,input::get("field"));
                break;
            case 'same_station':
                $fileName =  $this->exportFile(2, $dimension, $city, input::get("field"));
                break;
            case 'azimuth_check':
                $fileName =  $this->exportFile(3, $dimension, $city, input::get("field"));
                break;
            case 'lon_lat_check':
                $fileName =  $this->exportFile(4, $dimension, $city, input::get("field"));
                break;
        }
        
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }


    /**
     * 工参数据查询导出功能
     *
     * @return mixed
     */
    public function exportFile($action, $dimension, $city , $fieldObj)
    {
        $table = "";
        switch ($action) {
            case '1':
                $conn = new Report_100mCoEarfcnSiteCheck;
                $table = "Report_100mCoEarfcnSiteCheck";
                $date = Report_100mCoEarfcnSiteCheck::orderBy('date_id','desc')->first(['date_id'])->toArray()['date_id'];
                $filename = "common/files/异常基站站点数据_跨站100米内同频检查_" . $date . ".csv";
                break;
            case '2':
                $conn = new Report_60degreeSectorDirCheck;
                $table = "Report_60degreeSectorDirCheck";
                $date = Report_60degreeSectorDirCheck::orderBy('date_id','desc')->first(['date_id'])->toArray()['date_id'];
                $filename = "common/files/异常基站站点数据_站内同频小区方位角检查_" . $date .".csv";
                break;
            case '3':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dir','>45degree');
                $table = "Report_LatLonDirCheck_Combined";
                $date = Report_LatLonDirCheck_Combined::orderBy('date_id','desc')->first(['date_id'])->toArray()['date_id'];
                $filename = "common/files/异常基站站点数据_方位角检查_" . $date .".csv";
                break;
            case '4':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dist','>1km');
                $table = "Report_LatLonDirCheck_Combined";
                $date = Report_LatLonDirCheck_Combined::orderBy('date_id','desc')->first(['date_id'])->toArray()['date_id'];
                $filename = "common/files/异常基站站点数据_经纬度检查_" . $date .".csv";
                break;
        }
        if ($dimension == "city") {
            $conn = $conn->where('s.city',$city);
        }
        $fieldArr = [];
        $textArr = [];
        foreach ($fieldObj as $field) {
            $f = $field['field'];
            if ($f == "ECGI" || $f == "cellType" || $f == "earfcn") {
                $f = $table.".".$f;
            }
            if ($action == 1) {
                if ($f == "ECI") {
                    $f = "s.ECI";
                }
                if ($f == "n_ECI") {
                    $f = "t.ECI as n_ECI";
                }
                if ($f == "cellNameChinese") {
                    $f = "s.cellNameChinese";
                }
                if ($f == "n_cellNameChinese") {
                    $f = "t.cellNameChinese as n_cellNameChinese";
                }
                if ($f == "siteNameChinese") {
                    $f = "s.siteNameChinese";
                }
                if ($f == "n_siteNameChinese") {
                    $f = "t.siteNameChinese as n_siteNameChinese";
                }
                if ($f == "cluster") {
                    $f = "s.cluster";
                }
                if ($f == "n_cluster") {
                    $f = "t.cluster as n_cluster";
                }
                if ($f == "duplexMode") {
                    $f = "s.duplexMode";
                }
                if ($f == "city") {
                    $f = "s.city";
                }
            }
            array_push($fieldArr, $f);
            array_push($textArr, $field['title']);
        }

        $conn = $conn->where('date_id', $date)
                        ->leftJoin('mongs.siteLte as s', 's.ecgi', '=', $table.'.ECGI');
        $columns = $fieldArr;
        if ($action == 1) {
            $conn = $conn->leftJoin('mongs.siteLte as t', 't.ecgi', '=', $table.'.n_ECGI');
        }
        $rows = $conn->get($columns)
                    ->toArray();
        $result['text'] = implode(",", $textArr);

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK', 'UTF-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            $temp = "";
            foreach ($row as $key => $value) {
                $temp .= $value.",";
            }
            $temp = substr($temp, 0, -1);
            fwrite($fp, mb_convert_encoding($temp. "\n", 'GBK', 'UTF-8'));
        }
        fclose($fp);

        return $filename;
    }
}//end class
