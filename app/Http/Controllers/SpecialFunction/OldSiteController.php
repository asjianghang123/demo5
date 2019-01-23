<?php 

/**
     * OldSiteController.php
     * @category SystemManage
     * @package  App\Http\Controllers\SystemManage
     * @author   ericsson <genius@ericsson.com>
     * @license  MIT License
     * @link     https://laravel.com/docs/5.4/controllers
     */
namespace App\Http\Controllers\SpecialFunction;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use DateTime;
use DateInterval;
use Config;
use App\Models\NEWSITESTATE\OldSite;
use App\Models\Mongs\Task;
use App\Http\Controllers\Common\MyRedis;
/**
 * 新站追踪
 * Class OldSiteController
 *
 * @category SpecialFunction
 * @package  App\Http\Controllers\SpecialFunction
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class OldSiteController extends Controller
{
    /**
     * 获得城市列表
     *
     * @return array 城市记录
     */
    public function getCitys()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }//end of getCitys
    /**
     * 获得表头字段
     *
     * @return array 表头字段集
     */
    public function getTableField()
    {
        $citys = Input::get('citys');
        $eNodeBId        = Input::get('eNodeBId');
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $dbc = new DataBaseConnection();

        $filter = "";
        if ($citys) {
            $cityArr = array();
            foreach ($citys as $cityChinese) {
                array_push($cityArr, $dbc->getCityByCityChinese($cityChinese)[0]->connName);
                $cityIn = "('".implode("','", $cityArr)."')";
            }
            $filter = $filter." and city in $cityIn ";
        }

        if ($startTime && $endTime) {
            $filter = $filter." and date(datetime_id) between '$startTime' and '$endTime'";
        }
        if ($eNodeBId) {
            $filter = $filter." and ecgi like '%$eNodeBId%'";
        }
        if ($filter != "") {
            $filter = substr($filter, 4);
            $row = OldSite::whereRaw($filter)->first();
        }else{
            $row = OldSite::first();
        }
        $data = array();
        if ($row) {
            $row = $row->toArray();
            foreach ($row as $key => $value) {
                $data[trans('message.newSite.'.$key)] = $value;
            }
        }else{

            $data['result'] = "error";
        }
        return $data;
    }//end of getTableField
    /**
     * 获得新站信息
     *
     * @return array 记录
     */
    public function getOldSite()
    {   
        $citys = Input::get('citys');
        $eNodeBId        = Input::get('eNodeBId');
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $page = Input::get('limit');
        $dbc = new DataBaseConnection();

        $filter = "";
        if ($citys) {
            $cityArr = array();
            foreach ($citys as $cityChinese) {
                array_push($cityArr, $dbc->getCityByCityChinese($cityChinese)[0]->connName);
                $cityIn = "('".implode("','", $cityArr)."')";
            }
            $filter = $filter." and city in $cityIn ";
        }

        if ($startTime && $endTime) {
            $filter = $filter." and date(datetime_id) between '$startTime' and '$endTime'";
        }
        if ($eNodeBId) {
            $filter = $filter." and ecgi like '%$eNodeBId%'";
        }

        if ($filter != "") {
            $filter = substr($filter, 4);
            $rows = OldSite::whereRaw($filter)->orderBy('datetime_id','desc')->paginate($page)->toArray();
        }else{
            $rows = OldSite::orderBy('datetime_id','desc')->paginate($page)->toArray();
        }
        $items = array();
        foreach ($rows['data'] as $row) {
            $item = array();
            foreach ($row as $key => $value) {
                $item[trans('message.newSite.'.$key)] = $value;
            }
            array_push($items, $item);
        }
        $result         = array();
        $result["total"] = $rows['total'];
        $result['rows'] = $items;
        return json_encode($result);

    }//end getNEWSITE()

    /**
     * 导出新站记录
     *
     * @return array 导出结果
     */
    public function exportAllSearch()
    {
        $citys = Input::get('citys');
        $eNodeBId        = Input::get('eNodeBId');
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $dbc = new DataBaseConnection();

        $filter = "";
        if ($citys) {
            $cityArr = array();
            foreach ($citys as $cityChinese) {
                array_push($cityArr, $dbc->getCityByCityChinese($cityChinese)[0]->connName);
                $cityIn = "('".implode("','", $cityArr)."')";
            }
            $filter = $filter." and city in $cityIn ";
        }

        if ($startTime && $endTime) {
            $filter = $filter." and date(datetime_id) between '$startTime' and '$endTime'";
        }
        if ($eNodeBId) {
            $filter = $filter." and ecgi like '%$eNodeBId%'";
        }

        if ($filter != "") {
            $filter = substr($filter, 4);
            $items = OldSite::whereRaw($filter)->orderBy('datetime_id','desc')->get()->toArray();
        }else{
            $items = OldSite::orderBy('datetime_id','desc')->get()->toArray();
        }
        $keys = array();
        foreach (array_keys($items[0]) as $key) {
            array_push($keys, trans("message.newSite.".$key));
        }

        $column = implode(",",$keys);

        $result = array();
        $fileName = "common/files/新站信息_" . date('YmdHis') . ".csv";
        //$column = "datetime_id,city,ecgi,tac,ipAddr";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
        
    }//end exportAllSearch()
    /**
     * 导出新站中ipAddr为空的记录
     *
     * @return array 导出结果
     */
    public function exportOldSite()
    {
        $citys = Input::get('citys');
        
        $eNodeBId        = Input::get('eNodeBId');
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $dbc = new DataBaseConnection();

        $filter = "";
        if ($citys) {
            $cityArr = array();
            foreach ($citys as $cityChinese) {
                array_push($cityArr, $dbc->getCityByCityChinese($cityChinese)[0]->connName);
                $cityIn = "('".implode("','", $cityArr)."')";
            }
            $filter = $filter." and city in $cityIn ";
        }

        if ($startTime && $endTime) {
            $filter = $filter." and date(datetime_id) between '$startTime' and '$endTime'";
        }
        if ($eNodeBId) {
            $filter = $filter." and ecgi like '%$eNodeBId%'";
        }

        if ($filter != "") {
            $filter = substr($filter, 4);
            $items = OldSite::select('datetime_id','city','ecgi','tac','ipAddr')->whereRaw($filter)->where('IsExistKget','N')->whereRaw(" (ipAddr = '' or ipAddr is null or IsGetKget!='Y') ")->orderBy('datetime_id','desc')->get()->toArray();
        }else{
            $items = OldSite::select('datetime_id','city','ecgi','tac','ipAddr')->where('IsExistKget','N')->whereRaw(" (ipAddr = '' or ipAddr is null or IsGetKget!='Y') ")->orderBy('datetime_id','desc')->get()->toArray();
        }
        $keys = array();
        foreach (array_keys($items[0]) as $key) {
            array_push($keys, trans("message.newSite.".$key));
        }

        $column = implode(",",$keys);
        $result = array();
        $fileName = "common/files/新站信息_" . date('YmdHis') . ".csv";
        //$column = "datetime_id,city,ecgi,tac,ipAddr";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
        
    }//end exportNEWSITE()
    /**
     * 读取CSV文件
     *
     * @return mixed
     */
    public function getFileContent()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result <= 1) {
            return "lt1";
        }else if($len_result > 51){
            return "gt50";
        }
        $ipList = array();
        for ($i=1; $i < $len_result; $i++) { 
            $datetime_id = $result[$i][0];
            $city = $result[$i][1];
            $ecgi = $result[$i][2];
            $tac = $result[$i][3];
            $ipAddr = $result[$i][4];
            
            if ($ipAddr != '') {
                OldSite::where('datetime_id', $datetime_id)->where('city', $city)->where('ecgi', $ecgi)->where('tac', $tac)->whereRaw(" IsGetKget != 'Y' ")->update(array('ipAddr' => $ipAddr));
                $count = OldSite::where('datetime_id', $datetime_id)->where('city', $city)->where('ecgi', $ecgi)->where('tac', $tac)->where('IsExistKget','N')->whereRaw(" IsGetKget != 'Y' ")->count();
                if ($count > 0){
                    if (array_key_exists($city, $ipList)) {
                        if (!in_array($ipAddr, $ipList[$city])) {
                            $ipList[$city][]= $ipAddr;
                        }
                    }else{
                        $ipList[$city][]= $ipAddr;
                    }
                }
            }
            
        }
        $data = array();
        //$data['info'] = $result;
        $data['ipList'] = $ipList;
        return $data;
    }//end getFileContent()

    /**
     * 获取kget日志，kget解析入库，执行检查算法，生成ipList.txt，发送ipList.txt文件到docker
     *
     * @return mixed
     */
    public function runTask(){
        $ipList = Input::get('ipList');
        //$result = Input::get('info');
        //$len_result = count($result);
        if ($ipList) {
            include_once("/opt/lampp/htdocs/genius/app/Http/Controllers/SpecialFunction/OldSiteKget.php");
            $siteKget = new \OldSiteKget();
            $siteKget->sendInfo2Docker($ipList, "", "web");
        }
        return;
    }//end runTask()

    /**
     * 获取kget与分发docker
     *
     * @return mixed
     */
    public function sendInfo2Docker($ipList){
        $nodeList = Config::get("sitekget.nodeList");
        //获取kget日志,并解析入库
        $date = new DateTime();
        $dbName = "kget".$date->format('ymd');
        $count = Task::where('taskName', '=', $dbName)->count();
        if ($count == 0) {
            $dbName = "kget".$date->sub(new DateInterval('P1D'))->format('ymd');
        }
        foreach ($ipList as $city => $ipAddrs) {
            $nodeIp = $nodeList[$city]['ip'];
            $user = $nodeList[$city]['user'];
            $password = $nodeList[$city]['password'];
            foreach ($ipAddrs as $ipAddr) {
                $command = "sudo /opt/gback/gtools/loginSiteGetKget/siteGetKget.sh $nodeIp $user $password $ipAddr $dbName";
                print_r($command);
                exec($command);
            }
        }

        //分发ipList到docker
        foreach ($ipList as $city => $ipAddrs) {
            $fileName = "/data/trace/siteKget/ipList/ipList_".$city."_".$this->udate('YmdHisu').".txt";
            $myfile = fopen($fileName, "a+") or die("Unable to open file!");
            $content = "";
            foreach ($ipAddrs as $ipAddr) {
                $content = $content.$ipAddr."\r\n";
            }
            fwrite($myfile, $content);
            fclose($myfile);
            $remoteIp = $nodeList[$city]['ip'];
            $remoteFile = "/data/ftpcfg";
            $command = "sudo /opt/gback/gtools/loginSiteGetKget/runScpExpect.sh $fileName $remoteIp $remoteFile";
            print_r($command);
            exec($command);
        }
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
    }
    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表
     */
    public function getDate()
    {   
       $dbc    = new DataBaseConnection();
        $citys  = Input::get('citys');
        $db     = $dbc->getDB('mongs', 'mongs');
        $table  = 'OldSiteOpenRemind';
        if ($citys && count($citys) == 1) {
            foreach ($citys as $cityChinese) {
                $city = $dbc->getCityByCityChinese($cityChinese)[0]->connName;
                $sql    = "select distinct substring_index(datetime_id,' ',1) datetime_id from $table where city='$city'";
                $this->type = 'mongs'.':OldSiteOpenRemind:'.$city;
                return $this->getValue($db, $sql);
            }
        }else{
            $sql    = "select distinct substring_index(datetime_id,' ',1) datetime_id from $table";
            $this->type = 'mongs'.':OldSiteOpenRemind';
            return $this->getValue($db, $sql);
        }


    }//end getDate()
}