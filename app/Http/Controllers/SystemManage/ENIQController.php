<?php

/**
 * ENIQController.php
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
use App\Http\Requests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\Databaseconn2G;
use App\Models\Mongs\Alarm4GServerInfo;
use App\Models\Mongs\Alarm2GServerInfo;

/**
 * ENIQ管理
 * Class ENIQController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ENIQController extends Controller
{


    /**
     * 获得4GENIQ信息
     *
     * @return mixed
     */
    public function query4G()
    {
        $row = Databaseconns::get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            $host     = $qr['host'];
            $port     = $qr['port'];
            $dbName   = $qr['dbName'];
            $userName = $qr['userName'];
            $password = $qr['password'];
            $pmDbDSN  = "dblib:host=".$host.":".$port.";".((float)phpversion()>7.0?'dbName':'dbname')."=".$dbName;
            try {
                $pmDB = new PDO($pmDbDSN, $userName, $password);
                if ($pmDB == null) {
                    $qr['status'] = "false";
                } else {
                    $qr['status'] = "true";
                }
            } catch (Exception $e) {
                $qr['status'] = "false";
            }
            array_push($items, $qr);
        }
        $result         = array();
        $result['text'] = 'status,connName,cityChinese,host,port,dbName,userName,password,subNetwork,subNetworkFdd,subNetworkNbiot';
        $result['rows'] = $items;
        return json_encode($result);
    }//end Query4G()


    /**
     * 获得2GENIQ信息
     *
     * @return mixed
     */
    public function query2G()
    {
        $row = Databaseconn2G::get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            $host     = $qr['host'];
            $port     = $qr['port'];
            $dbName   = $qr['dbName'];
            $userName = $qr['userName'];
            $password = $qr['password'];
            $pmDbDSN  = "dblib:host=".$host.":".$port.";".((float)phpversion()>7.0?'dbName':'dbname')."=".$dbName;
            try {
                $pmDB = new PDO($pmDbDSN, $userName, $password);
                if ($pmDB == null) {
                    $qr['status'] = "false";
                } else {
                    $qr['status'] = "true";
                }
            } catch (Exception $e) {
                $qr['status'] = "false";
            }
            array_push($items, $qr);
        }
        $result         = array();
        $result['text'] = 'status,connName,cityChinese,host,port,dbName,userName,password';
        $result['rows'] = $items;
        return json_encode($result);
    }//end Query2G()

    /**
     * 获得告警ENIQ信息
     *
     * @return mixed
     */
    public function query4GAlarm()
    {
        $row = Alarm4GServerInfo::get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            $host     = $qr['host'];
            $port     = $qr['port'];
            $dbName   = $qr['dbName'];
            $userName = $qr['userName'];
            $password = $qr['password'];
            $pmDbDSN  = "dblib:host=".$host.":".$port.";".((float)phpversion()>7.0?'dbName':'dbname')."=".$dbName;
            try {
                $pmDB = new PDO($pmDbDSN, $userName, $password);
                if ($pmDB == null) {
                    $qr['status'] = "false";
                } else {
                    $qr['status'] = "true";
                }
            } catch (Exception $e) {
                $qr['status'] = "false";
            }
            array_push($items, $qr);
        }
        $result         = array();
        $result['text'] = 'status,serverName,city,host,port,dbName,userName,password';
        $result['rows'] = $items;
        return json_encode($result);
    }//end QueryAlarm()

    public function query2GAlarm()
    {
        $row = Alarm2GServerInfo::get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            $host     = $qr['host'];
            $port     = $qr['port'];
            $dbName   = $qr['dbName'];
            $userName = $qr['userName'];
            $password = $qr['password'];
            $pmDbDSN  = "dblib:host=".$host.":".$port.";".((float)phpversion()>7.0?'dbName':'dbname')."=".$dbName;
            try {
                $pmDB = new PDO($pmDbDSN, $userName, $password);
                if ($pmDB == null) {
                    $qr['status'] = "false";
                } else {
                    $qr['status'] = "true";
                }
            } catch (Exception $e) {
                $qr['status'] = "false";
            }
            array_push($items, $qr);
        }
        $result         = array();
        $result['text'] = 'status,serverName,city,host,port,dbName,userName,password';
        $result['rows'] = $items;
        return json_encode($result);
    }//end QueryAlarm()


    


    /**
     * 更新ENIQ信息
     *
     * @return void
     */
    public function updateENIQ()
    {
        $id            = input::get("ENIQId");
        $sign          = input::get("ENIQSign");
        $connName      = input::get("connName");
        $cityChinese   = input::get("cityChinese");
        $host          = input::get("host");
        $port          = input::get("port");
        $dbName        = input::get("dbName");
        $userName      = input::get("userName");
        $password      = input::get("password");
        $subNetwork    = input::get("subNetwork");
        $subNetworkFdd = input::get("subNetworkFdd");
        $subNetworkNbiot=input::get("subNetworkNbiot");
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');
        
        if ($id) {
            if ($sign == "4G") {
                Databaseconns::where('id', $id)->update([
                                                'connName'=>$connName,
                                                'cityChinese'=>$cityChinese,
                                                'host'=>$host,
                                                'port'=>$port,
                                                'dbName'=>$dbName,
                                                'userName'=>$userName,
                                                'password'=>$password,
                                                'subNetwork'=>$subNetwork,
                                                'subNetworkFdd'=>$subNetworkFdd,
                                                'subNetworkNbiot'=>$subNetworkNbiot
                                            ]);
            } else if ($sign == "2G") {
                Databaseconn2G::where('id', $id)->update([
                                                'connName'=>$connName,
                                                'cityChinese'=>$cityChinese,
                                                'host'=>$host,
                                                'port'=>$port,
                                                'dbName'=>$dbName,
                                                'userName'=>$userName,
                                                'password'=>$password
                                            ]);
            }
        } else {
            if ($sign == "4G") {
                $new4G = new Databaseconns;
                $new4G->connName = $connName;
                $new4G->cityChinese = $cityChinese;
                $new4G->host = $host;
                $new4G->port = $port;
                $new4G->dbName = $dbName;
                $new4G->userName = $userName;
                $new4G->password = $password;
                $new4G->subNetwork = $subNetwork;
                $new4G->subNetworkFdd = $subNetworkFdd;
                $new4G->subNetworkNbiot=$subNetworkNbiot;
                $new4G->save();
            } else if ($sign == "2G") {
                $new2G = new Databaseconn2G;
                $new2G->connName = $connName;
                $new2G->cityChinese = $cityChinese;
                $new2G->host = $host;
                $new2G->port = $port;
                $new2G->dbName = $dbName;
                $new2G->userName = $userName;
                $new2G->password = $password;
                $new2G->save();
            }
        }
        return $sign;
    }//end updateENIQ()


    
    /**
     * 更新告警ENIQ信息
     *
     * @return string
     */
    public function updateAlarm()
    {
        $id         = input::get("alarmId");
        $serverName = input::get("alarmServerName");
        $sign       = input::get("alarmSign");
        $city       = input::get("alarmCity");
        $host       = input::get("alarmHost");
        $port       = input::get("alarmPort");
        $dbName     = input::get("alarmDbName");
        $userName   = input::get("alarmUserName");
        $password   = input::get("alarmPassword");
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');
        
        if ($id) {
            // $sql = "UPDATE alarmServerInfo SET serverName = '$serverName', city = '$city', host = '$host', port = '$port', dbName = '$dbName', userName = '$userName', password = '$password' where id = '$id'";
           if ($sign == "4GAlarm") {
            $res = Alarm4GServerInfo::where('id', $id)->update([
                                                'serverName'=>$serverName,
                                                'city'=>$city,
                                                'host'=>$host,
                                                'port'=>$port,
                                                'dbName'=>$dbName,
                                                'userName'=>$userName,
                                                'password'=>$password
                                            ]);
            } else if ($sign == "2GAlarm") {
                $res = Alarm2GServerInfo::where('id', $id)->update([
                                                'serverName'=>$serverName,
                                                'city'=>$city,
                                                'host'=>$host,
                                                'port'=>$port,
                                                'dbName'=>$dbName,
                                                'userName'=>$userName,
                                                'password'=>$password
                                            ]);
            }
        } else {
            if ($sign == "4GAlarm") {
            // $sql = "INSERT INTO alarmServerInfo VALUES (null,'$serverName','$city','$host','$port','$dbName','$userName','$password')";
            $new4GAlarm = new Alarm4GServerInfo;
            $new4GAlarm->serverName = $serverName;
            $new4GAlarm->city = $city;
            $new4GAlarm->host = $host;
            $new4GAlarm->port = $port;
            $new4GAlarm->dbName = $dbName;
            $new4GAlarm->userName = $userName;
            $new4GAlarm->password = $password;
            $res = $new4GAlarm->save();
        } else if ($sign == "2GAlarm") {
                $new2GAlarm = new Alarm2GServerInfo;
            $new2GAlarm->serverName = $serverName;
            $new2GAlarm->city = $city;
            $new2GAlarm->host = $host;
            $new2GAlarm->port = $port;
            $new2GAlarm->dbName = $dbName;
            $new2GAlarm->userName = $userName;
            $new2GAlarm->password = $password;
            $res = $new2GAlarm->save();
            }
        }

        // $res = $db->exec($sql);
        return strval($res);

    }//end updateAlarm()

    /**
     * 删除ENIQ信息
     *
     * @return mixed
     */
    public function deleteENIQ()
    {
        $id   = input::get("id");
        $sign = input::get("sign");
        if ($sign == "1") {
            Databaseconns::destroy($id);
        } else if ($sign == "2") {
            Databaseconn2G::destroy($id);
        } else if ($sign == "3"){
            Alarm4GServerInfo::destroy($id);
        } else {
            Alarm2GServerInfo::destroy($id);
        }
        return $sign;
    }//end deleteENIQ()

    /**
     * 删除告警ENIQ信息
     *
     * @return void
     */
    /*public function deleteAlarm()
    {
        $id  = input::get("id");
        $res = AlarmServerInfo::destroy($id);
        echo $res;
    }//end deleteAlarm()*/
}//end class
