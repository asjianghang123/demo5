<?php

/**
 * DataSourceController.php
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\TraceServerInfo;
use Illuminate\Support\Facades\Storage;
use Config;

/**
 * 数据源管理
 * Class DataSourceController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class DataSourceController extends Controller
{


    /**
     * 获得LOG类别
     *
     * @return mixed
     */
    public function getLogType()
    {
        $row = TraceServerInfo::query()->selectRaw('DISTINCT type')->get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            if ($qr['type'] != "ctr") {
                continue;
            }
            array_push($items, ["value" => $qr['type'], "text" => strtoupper($qr['type'])]);
        }
        return json_encode($items);
    }//end getLogType()


    /**
     * 获得TRACE-SERVER信息
     *
     * @return mixed
     */
    public function getNode()
    {
        $dbc     = new DataBaseConnection();
        $logType = Input::get("logType");
        $row = TraceServerInfo::where('type', $logType)->get()->toArray();
        $items   = array();
        foreach ($row as $qr) {
            array_push($items, ["value" => $qr['ipAddress'], "text" => $dbc->getCHCity($qr['city']), "sshUserName" => $qr['sshUserName'], "sshPassword" => $qr['sshPassword'], "fileDir" => $qr['fileDir']]);
        }
        return json_encode($items);
    }//end getNode()


    /**
     * 获得目录下文件名列表
     *
     * @return void
     */
    public function getFileName()
    {
        // $strServer         = input::get('remoteIp');
        // $strServerPort     = "22";
        // $strServerUsername = input::get('userName');
        // $strServerPassword = input::get('userPassword');
        // $resConnection     = ssh2_connect($strServer, $strServerPort);
        // $fileDir           = input::get("fileDir");
        // $array = array();
        // if (ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)) {
        //     $resSFTP = ssh2_sftp($resConnection);
        //     $files   = scandir("ssh2.sftp://".intval($resSFTP).$fileDir);
        //     $items   = array();
        //     foreach ($files as $file) {
        //         if ($file == '.') {
        //             continue;
        //         } else if ($file == '..') {
        //             continue;
        //         } else {
        //             $items['label'] = $file;
        //             $items['value'] = $file;
        //             array_push($array, $items);
        //         }
        //     }
        // }

        // echo json_encode($array);

        $fileDir           = input::get("fileDir");
        $remoteIp         = input::get('remoteIp');
        $type = Input::get("type");
        // print_r($type);
        $fileDir = str_replace("/data/trace/", "", $fileDir);
        $rows =TraceServerInfo::where("ipAddress", $remoteIp)->where("type", $type)->get()->toArray();
        // $fileDir = $rows[0]['fileDir'];
        $ftpdir = $rows[0]['fileDir'];
        $ftpdir = explode("/", $ftpdir);
        $ftpdir = end($ftpdir);
        $ftpUserName = $rows[0]['ftpUserName'];
        $ftpPassword = $rows[0]['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);
        $array = array();
        $files = Storage::disk('ftp')->directories($type."/".$ftpdir."/");
        foreach ($files as $file) {
            $file = explode("/", $file)[2];
            // print_r($file);
            $items['label'] = $file;
            $items['value'] = $file;
            array_push($array, $items);
        }
        echo json_encode($array);
    }//end getFileName()


    public function scpFiles() {
        $remoteIp = Input::get("remoteIp");
        $fileArr = Input::get("file");
        $type = Input::get("type");
        $rows =TraceServerInfo::where("ipAddress", $remoteIp)->where("type", $type)->get()->toArray();
        $fileDir = $rows[0]['fileDir'];
        $ftpUserName = $rows[0]['ftpUserName'];
        $ftpPassword = $rows[0]['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);
        $fileName = $type."_".date("YmdHis")."_".md5(time());
        // print_r($fileName);
        Storage::disk('local')->makeDirectory($fileName);
        Config::set("filesystems.disks.commFile.root", "common/files/".$fileName);
        // $config = Config::get("filesystems.disks.local");
        // print_r($config);return;
        foreach ($fileArr as $key => $file) {
            $file = substr($file, 0, -1);
            $time = substr(explode(".", $file)[0], 2) . substr(explode(".", $file)[1], 0, 2);
            // Storage::disk('local')->put($file, Storage::disk('ftp')->get('ctr/test'));
            // print_r('ctr/autobackup/'.$time.$file);
            // Storage::disk('commFile')->put($file, Storage::disk('ftp')->get('ctr/autobackup/'.$time.$file));
            Storage::disk('commFile')->put($file, Storage::disk('ftp')->get(str_replace("/data/trace/", "", $fileDir)."/".$time.$file));
            exec('tar -C common/files/ -cvzf common/files/'.$fileName.'.tar.gz '.$fileName);
        }
        echo "common/files/".$fileName.'.tar.gz';
        //Storage::copy(Storage::disk('ftp')->get('ctr/test'), "common/files/test");
        // Storage::disk('local')->put(Storage::disk('ftp')->get('ctr/test'));
        // Storage::disk('local')->put('test0126',Storage::disk('ftp')->get('ctr/test'));
    }

    public function postFileupload(Request $request){
        //判断请求中是否包含name=file的上传文件
        if(!$request->hasFile('file')){
            exit('上传文件为空！');
        } 
        $file = $request->file('file');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            exit('文件上传出错！');
        }
        $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        $savePath = 'test/'.$newFileName;
        $bytes = Storage::put(
            $savePath,
            file_get_contents($file->getRealPath())
        );
        if(!Storage::exists($savePath)){
            exit('保存文件失败！');
        }
        header("Content-Type: ".Storage::mimeType($savePath));
        echo Storage::get($savePath);
    }

    /**
     * 获得基站CTR文件列表
     *
     * @return void
     */
    public function ctrTreeItems()
    {
        // $config = Config::get("filesystems.disks.ftp");
        // print_r($config);
        //$erbs      = input::get('erbs');
        // $exists = Storage::disk('ftp')->exists('my.cnf');
        // $contents = Storage::disk('ftp')->url('ctr/test');
        ///// Storage::disk('local')->put($erbs,Storage::disk('ftp')->get('ctr/test'));
        // var_dump($contents);
           // var_dump($exists);

        $erbs      = input::get('erbs');
        $filesName = input::get('point');
        $erbsArr   = explode(',', $erbs);
        // var_dump($erbsArr);return;
        $type = input::get("type");
        $ftpip         = input::get('remoteIp');
        // $strServerPort     = "22";
        $city = "";
        // $strServerUsername = input::get('userName');
        // $strServerPassword = input::get('userPassword');
        $fileDir           = input::get("fileDir");
        if ($ftpip == "10.197.132.33") {
            $city = "changzhou";
        } elseif ($ftpip == "10.40.61.186") {
            $city = "wuxi";
        } elseif ($ftpip == "10.40.51.185") {
            $city = "nantong";
        } elseif ($ftpip == "10.40.83.221") {
            $city = "zhenjiang";
        }
        // $resConnection     = ssh2_connect($strServer, $strServerPort);
        $idNum         = 1;
        $allCtr        = array();
        $ctrTime       = array();
        $childrengz    = array();
        $allChildrengz = array();
        $succFilesName = array();
        // var_dump($remoteIp);var_dump($type);var_dump($city);return;
        $rows =TraceServerInfo::where("ipAddress", $ftpip)->where("type", $type)->where("city", $city)->get()->toArray();
        $remoteIp = $rows[0]["ipAddress"];
        // var_dump($remoteIp);return;
        $ftpdir = $rows[0]['fileDir'];
        $ftpdir = explode("/", $ftpdir);
        $ftpdir = end($ftpdir);
        $fileDir = $rows[0]['fileDir'];
        $ftpUserName = $rows[0]['ftpUserName'];
        $ftpPassword = $rows[0]['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);
        $file = Storage::disk('ftp')->directories($type."/".$ftpdir."/");
        // var_dump($filesName);return;
        foreach ($filesName as $fileName) {
            foreach ($file as $value) {
                if ($fileName != explode("/", $value)[count(explode("/", $value))-1]) {
                    continue;
                } else {
                    array_push($succFilesName, $fileName);
                    $ctrTime['id']      = $idNum;
                    $ctrTime['kpiName'] = $value; 
                    $idNum++;
                }
                // var_dump($ctrTime);
                array_push($allCtr, $ctrTime);
            }
        }
        // var_dump($allCtr);return;
        $idNum = 1;
        foreach ($succFilesName as $succFileName) {
            $childrenId = 1;
            $dirsgz     = $type."/".$ftpdir."/".$succFileName;;
            // var_dump($dirsgz);
            $filesgz    = $this->getFile($dirsgz);
            // var_dump($filesgz);
            foreach ($filesgz as $filegz) {
                foreach ($erbsArr as $erb) {
                    $filePos = strpos($filegz, $erb);
                    // var_dump($filePos);
                    if ($filePos == false) {
                        continue;
                    } else {
                        $allChildrengz['id']      = $idNum.$childrenId;
                        $allChildrengz['kpiName'] = str_replace($dirsgz, '', $filegz);
                        $allChildrengz['size'] = (round(Storage::disk("ftp")->size($filegz)/1024, 2))." KB";
                        $childrenId++;
                        array_push($childrengz, $allChildrengz);
                    }
                }
            }

            $num = ($idNum - 1);
            $allCtr[$num]['children'] = $childrengz;
            $childrengz = array();

            $idNum++;
        }
        // var_dump($allCtr);return;
        echo json_encode($allCtr);
        // if (ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)) {
        //     $resSFTP = ssh2_sftp($resConnection);
        //     $file    = scandir("ssh2.sftp://".intval($resSFTP).$fileDir);
        //     foreach ($filesName as $fileName) {
        //         foreach ($file as $value) {
        //             if ($value == '.' || $value == '..' || $fileName != $value) {
        //                 continue;
        //             } else {
        //                 array_push($succFilesName, $fileName);
        //                 $ctrTime['id']      = $idNum;
        //                 $ctrTime['kpiName'] = $value;
        //                 $idNum++;
        //             }

        //             array_push($allCtr, $ctrTime);
        //         }
        //     }

        //     // gz压缩包匹配
        //     $idNum = 1;
        //     foreach ($succFilesName as $succFileName) {
        //         $childrenId = 1;
        //         $dirsgz     = "ssh2.sftp://".intval($resSFTP).$fileDir."/$succFileName";
        //         $filesgz    = $this->getFile($dirsgz);
        //         foreach ($filesgz as $filegz) {
        //             foreach ($erbsArr as $erb) {
        //                 $filePos = strpos($filegz, $erb);
        //                 if ($filePos == false) {
        //                     continue;
        //                 } else {
        //                     $allChildrengz['id']      = $idNum.$childrenId;
        //                     $allChildrengz['kpiName'] = str_replace($dirsgz, '', $filegz);
        //                     ;
        //                     $allChildrengz['size'] = filesize($filegz).' B';
        //                     $childrenId++;
        //                     array_push($childrengz, $allChildrengz);
        //                 }
        //             }
        //         }

        //         $num = ($idNum - 1);
        //         $allCtr[$num]['children'] = $childrengz;
        //         $childrengz = array();

        //         $idNum++;
        //     }//end foreach
        // }//end if

        // echo json_encode($allCtr);

    }//end ctrTreeItems()


    /**
     * 获得目录下GZ文件列表
     *
     * @param string $dir 目录名
     *
     * @return array
     */
    public function getFile($dir)
    {
         $fileArr = array();
        $file     = storage::disk("ftp")->files($dir);
        if ($file) {
            krsort($file);
            foreach ($file as $value) {
                if ($value != "." && $value != "..") {
                    if (!strpos($value, ".gz")) {
                        $fileArr = array_merge($fileArr, $this->getFile($value));
                    } else {
                        array_push($fileArr, $value);
                    }
                }
            }
        }

        return $fileArr;

    }//end getFile()


    /**
     * 在线入库
     *
     * @return void
     */
    public function onlineStorage()
    {
        $type = input::get("type");
        if ($type == 'CTRFULL') {
            $type = "CTR";
        }

        $remoteIp    = input::get("remoteIp");
        $gzFile      = explode(";;", input::get("gzFiles"));
        $baseStation = input::get("baseStation");
        date_default_timezone_set("PRC");
        $dirName    = $baseStation."_".date("YmdHi");
        $type       = strtolower($type);
        $new_folder = "/data/trace/".$type."/".$dirName;
        mkdir($new_folder, 0777);
        chmod($new_folder, 0777);

        foreach ($gzFile as $file) {
            $folderName = explode(".", explode("/", $file)[2])[0];
            $hour       = substr(explode(".", explode("/", $file)[2])[1], 0, 2);
            $folderName = substr($folderName, 1);
            if ($remoteIp == "10.197.132.33") {
                // $folderName = "/data/trace/".$type."/autobackup/changzhou/".$folderName.$hour.$file;
                $folderName = "/data/webdata/".$type."/autobackup/".$folderName.$hour.$file;
            } else {
                $folderName = "/data/webdata/".$type."/autobackup/".$folderName.$hour.$file;
            }

            $scp = "sudo scp -r lijian@".$remoteIp.":".$folderName." ".$new_folder."/".explode("/", $file)[2];
            // echo $scp;
            exec($scp);
        }

        echo $dirName;

    }//end onlineStorage()


    /**
     * 在线数据管理
     *
     * @return void
     */
    public function storage()
    {
        // echo '123';
        $type     = input::get("type");
        $remoteIp = input::get("remoteIp");
        $gzFile   = explode(";;", input::get("gzFiles"));
        $fileDir  = input::get("fileDir");
        $filectrname = Input::get("fileName")[0];
        // var_dump($filectrname);
        // print_r($remoteIp);
        // $city = 'changzhou';
        /*if ($remoteIp == "10.197.132.33") {
            $city = "changzhou";
            $n    = 1;
        } else if ($remoteIp == "10.40.61.186") {
            $city = "wuxi";
            $n    = 1;
        } else if ($remoteIp == "10.40.51.185") {
            $city = "nantong";
            $n    = 1;
        } else if ($remoteIp == "10.40.83.221") {
            $city = "zhenjiang";
            $n    = 1;
        }*/

        $rows = TraceServerInfo::where("ipAddress", $remoteIp)->where("type", $type)->first()->toArray();
        // print_r($rows);return;
        $city = $rows['city'];
        $n = 1;

        // $fileDir = $rows[0]['fileDir'];
        $ftpUserName = $rows['ftpUserName'];
        $ftpPassword = $rows['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);
        $fileName = $type."_".date("YmdHis")."_".md5(time());
        $user = Auth::user()->user;
        date_default_timezone_set("PRC");
        $dirName    = $type."_".$city."_".time();
        $type       = strtolower($type);
        $new_folder = "/data/webdata/".$type."/".$user."/".$dirName;
        // echo $new_folder;
        // Storage::disk('local')->makeDirectory($new_folder);
        Config::set("filesystems.disks.commFile.root", $new_folder);

        
        mkdir($new_folder, 0777, true);
        chmod($new_folder, 0777);

        foreach ($gzFile as $file) {
            // print_r($file);
            $folderName = explode(".", explode("/", $file)[$n])[0];
            $hour       = substr(explode(".", explode("/", $file)[$n])[1], 0, 2);
            // print_r($hour);
            $folderName = substr($folderName, 1);
            // print_r($folderName);
            $folderName = $fileDir."/".$filectrname.$file;
            // print_r($folderName);
            Storage::disk('commFile')->put($file, Storage::disk('ftp')->get(str_replace("/data/trace/", "", $folderName)));
            // $scp        = "sudo scp -r lijian@".$remoteIp.":".$folderName." ".$new_folder."/".explode("/", $file)[$n];
            // echo $scp;
            // exec($scp);
        }

        echo $new_folder;

    }//end storage()


    /**
     * 上传文件
     *
     * @return void
     */
    public function uploadFile()
    {
        $filename = $_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo '请选择要导入的文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }

        move_uploaded_file($filename, "common/files/".$_FILES['fileImport']['name']);

        setlocale(LC_ALL, null);
        $files = file("common/files/".$_FILES['fileImport']['name']);
        $text  = array();
        foreach ($files as $txt) {
            array_push($text, $txt);
        }

        $textStr = implode(",", $text);
        print_r($textStr);

    }//end uploadFile()

    public function deleteAutoDir()
    {
        $path = input::get("tracePath");
        exec("rm -R $path");
    }


}//end class
