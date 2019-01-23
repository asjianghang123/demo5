<?php

/**
 * LocalDataManageController.php
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
use App\Models\Mongs\Task;

/**
 * 本地数据管理
 * Class LocalDataManageController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LocalDataManageController extends Controller
{


    /**
     * 上传log文件
     *
     * @return void
     */
    public function uploadFile()
    {
        $new_folder = input::get("dirName");
        $type    = input::get("type");
        $id      = input::get("id");
        $user = Auth::user()->user;
        $filename   = $_FILES[$id]['tmp_name'];
        if (!is_dir($new_folder)) {
            mkdir($new_folder, 0777, true);
            chmod($new_folder, 0777);
        }

        $result = move_uploaded_file($filename, $new_folder."/".$_FILES[$id]['name']);
        if ($result) {
            echo $_FILES[$id]['name'];
        } else {
            echo "error";
        }

    }//end uploadFile()

    /**
     * 查询用户的ctr目录
     *
     * @return void
     */
    public function getDir()
    {
        // $type = "ctr";
        $types = ["kget","ctr","cdr","ebm","pcap"];
        $user = Auth::user()->user;

        $result = array();
        foreach ($types as $key => $type) {
            $dir  = '/data/trace/'.$type.'/'.$user;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                chmod($dir, 0777);
            }
            $root = [
                "id"=>$key,
                "text"=>strtoupper($type),
                "path"=>$dir,
                "iconCls"=>"icon-blank",
                "type"=>$type
            ];
            $root['nodes'] = $this->tree($dir, $key, $type);
            array_push($result, $root);
        }
        echo json_encode($result);
    }//end getDir()

    public function tree($directory, $pid, $type)
    {
        $mydir   = opendir($directory);
        $i       = 1;
        $content = array();
        while ($file = readdir($mydir)) {
            if ((is_dir("$directory/$file")) && ($file != ".") && ($file != "..")) {
                $nodes = $this->tree("$directory/$file", $pid.$i, $type);
                $temp = [
                    "id"=>$pid.$i,
                    "text"=>$file,
                    "iconCls"=>"icon-blank",
                    "path"=>$directory."/".$file,
                    "type"=>$type
                ];
                if ($nodes) {
                    $temp['nodes'] = $nodes;
                }
                $content[($i - 1)] = $temp;
                $i = ($i + 1);

            }//end if
        }//end while
        closedir($mydir);
        return $content;
    }

    /**
     * 新增目录
     *
     * @return void
     */
    public function addDir()
    {
        $dir = input::get("path");
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
            echo true;
        } else {
            echo false;
        }
    }//end addDir()

    public function deleteDir()
    {
        $dir = input::get("path");
        if (is_dir($dir)) {
            exec("rm -R $dir");
            echo true;
        } else {
            echo false;
        }
    }

    public function getFileByDir()
    {
        $directory = input::get("path");
        $mydir = opendir($directory);

        $content = array();
        while ($file = readdir($mydir)) {
            if (is_file("$directory/$file")) {
                $temp = [
                    "text"=>$file
                ];
                $content[] = $temp;
            }//end if
        }//end while
        closedir($mydir);
        echo json_encode($content);
    }

    public function analysisLog()
    {
        $directory = input::get('path');
        $new_folder = str_replace('/ctr/', '/ltetoolsOutput/', $directory);
        if (is_dir($new_folder)) {
            exec("rm -f ".$new_folder."/*");
        } else {
            mkdir($new_folder, 0777, true);
            chmod($new_folder, 0777);
        }
        $mydir = opendir($directory);
        while ($file = readdir($mydir)) {
            if (is_file("$directory/$file")) {
                $logName = $directory."/".$file;
                $txtName = $new_folder."/".$file.".txt";

                $command = "sudo common/sh/start_ltetools.sh ".$logName." ".$txtName;
                system($command);
            }//end if
        }//end while
        closedir($mydir);
        echo "true";
    }

    public function getLogByDir()
    {
        $directory = input::get("path");
        $directory = str_replace('/ctr/', '/ltetoolsOutput/', $directory);
        if (!is_dir($directory)) {
            $result['content'] = [];
            $result['dir'] = $directory;
            echo json_encode($result);
            return;
        }
        $mydir = opendir($directory);

        $content = array();
        while ($file = readdir($mydir)) {
            if (is_file("$directory/$file")) {
                $temp = [
                    "text"=>$file
                ];
                $content[] = $temp;
            }//end if
        }//end while
        closedir($mydir);
        $result['content'] = $content;
        $result['dir'] = $directory;
        echo json_encode($result);
    }

    public function addTask()
    {
        $taskName = input::get("taskName");
        $result   = array();

        if (Task::where('taskName', $taskName)->exists()) {
            $result['state'] = 1;
        } else {
            $type       = input::get("type");
            $createTime = input::get("createTime");
            $tracePath  = input::get("tracePath");
            $owner = Auth::user()->user;
            $newTask = new Task;
            $newTask->taskName = $taskName;
            $newTask->status = 'prepare';
            $newTask->tracePath = $tracePath;
            $newTask->owner = $owner;
            $newTask->createTime = $createTime;
            $newTask->type = $type;
            $newTask->save();

            $result['state'] = 0;
        }
        return json_encode($result);
    }//end addTask()


}//end class
