<?php

/**
 * StorageController.php
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
use MongoClient;
use MongoCode;
use MongoId;
use PDO;
use App\Models\Mongs\Task;

/**
 * 入库管理
 * Class StorageController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class StorageController extends Controller
{


    /**
     * 获得任务信息列表
     *
     * @return mixed
     */
    public function taskQuery()
    {
        $id  = input::get("id");
        if ($id == 1) {
            $type = "parameter";
        } else if ($id == 2) {
            $type = "ctrsystem";
        } else if ($id == 3) {
            $type = "cdrsystem";
        } else if ($id == 4) {
            $type = "ebmsystem";
        } else if ($id == 5) {
            $type = "pcapsystem";
        } else if ($id == 6) {
            $type = "ctrfullsystem";
        }

        if ($id == 0) {
            $row = Task::get()->sortByDesc('createTime')->toArray();
        } else {
            $row = Task::where('type', $type)->get()->sortByDesc('createTime')->toArray();
        }

        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result         = array();
        $result['text'] = 'taskName,status,startTime,endTime,tracePath,owner,createTime,type';
        $result['rows'] = $items;
        return json_encode($result);

    }//end taskQuery()


    /**
     * 获得Trace文件目录
     *
     * @return void
     */
    public function getTaskTraceDir()
    {
        $type       = input::get("type");
        // $type = $this->switchType($type);
        $user = Auth::user()->user;
        // $dir        = '/data/trace';
        $dir        = '/data/webdata/'.$type.'/'.$user;
        // $ctrfulldir = '/data/trace187/ctr';
        // $ctrfulldir        = '/data/trace/ctr';
        // if (is_dir($dir)) {
            // echo '['.$this->tree($dir, 1, false, $type, $ctrfulldir).']';
            
        // }
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        echo '['.$this->tree($dir, 1).']';

    }//end getTaskTraceDir()

    public function tree($directory, $pid)
    {
        $mydir   = opendir($directory);
        $i       = 1;
        $content = array();
        while ($file = readdir($mydir)) {
            if ((is_dir("$directory/$file")) && ($file != ".") && ($file != "..")) {
                $nodes = $this->tree("$directory/$file", $pid.$i);
                if ($nodes) {
                    $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                } else {
                    $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                }

                $i = ($i + 1);

            }//end if
        }//end while

        closedir($mydir);
        return implode(",", $content);

    }

    /**
     * 获得新建任务目录结构数
     *
     * @param string $directory  目录名
     * @param int    $pid        ID
     * @param bool   $flag       本地|在线
     * @param string $type       log类别
     * @param string $ctrfulldir 本地CTR目录
     *
     * @return string
     */
    public function tree1($directory, $pid, $flag, $type, $ctrfulldir)
    {
        $mydir   = opendir($directory);
        $i       = 1;
        $content = array();
        while ($file = readdir($mydir)) {
            if ((is_dir("$directory/$file")) && ($file != ".") && ($file != "..")) {
                if ($type == 'parameter' && ($file == 'kget' | $flag)) {
                    $nodes = $this->tree("$directory/$file", $pid.$i, true, $type, $ctrfulldir);
                    if ($nodes) {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                    } else {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                    }

                    $i = ($i + 1);
                }

                if ($type == 'ctrsystem' && ($file == 'ctr' | $flag)) {
                    $nodes = $this->tree("$directory/$file", $pid.$i, true, $type, $ctrfulldir);
                    if ($nodes) {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                    } else {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                    }

                    $i = ($i + 1);
                }

                if ($type == 'ctrfullsystem' && ($file == 'ctr' | $flag)) {
                    $nodes = $this->tree("$directory/$file", $pid.$i, true, $type, $ctrfulldir);
                    if ($nodes) {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$ctrfulldir/$file".'","nodes":['.$nodes.']}';
                    } else {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$ctrfulldir/$file".'"}';
                    }

                    $i = ($i + 1);
                }

                if ($type == 'cdrsystem' && ($file == 'cdr' | $flag)) {
                    $nodes = $this->tree("$directory/$file", $pid.$i, true, $type, $ctrfulldir);
                    if ($nodes) {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                    } else {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                    }

                    $i = ($i + 1);
                }

                if ($type == 'ebmsystem' && ($file == 'ebm' | $flag)) {
                    $nodes = $this->tree("$directory/$file", $pid.$i, true, $type, $ctrfulldir);
                    if ($nodes) {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                    } else {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                    }

                    $i = ($i + 1);
                }

                if ($type == 'pcapsystem' && ($file == 'pcap' | $flag)) {
                    $nodes = $this->tree("$directory/$file", $pid.$i, true, $type, $ctrfulldir);
                    if ($nodes) {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                    } else {
                        $content[($i - 1)] = '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                    }

                    $i = ($i + 1);
                }
            }//end if
        }//end while

        closedir($mydir);
        return implode(",", $content);

    }//end tree()


    /**
     * 新建任务
     *
     * @return mixed
     */
    public function addTask()
    {
        $taskName = input::get("taskName");
        if (Task::where('taskName', $taskName)->exists()) {
            return "false";
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
            return "true";
        }

    }//end addTask()


    /**
     * 删除任务
     *
     * @return mixed
     */
    public function deleteTask()
    {
        $taskName = input::get("taskName");
        $type     = input::get("type");

        Task::where('taskName', $taskName)->delete();
        if ($type == "pcapsystem") {
            $m = new MongoClient();
            // 连接
            $db = $m->pcap;
            // 获取名称为 "test" 的数据库
            $collection = $db->$taskName;
            $collection->drop();
        } else if ($type == "ctrfullsystem") {
            $m = new MongoClient("10.197.132.30:20000");
            // 连接
            $db = $m->ctr;
            // 获取名称为 "test" 的数据库
            $collection = $db->$taskName;
            $collection->drop();
        } else {
            DB::statement("DROP DATABASE IF EXISTS ".$taskName);
        }

        return "true";

    }//end deleteTask()


    /**
     * 获得任务执行日志
     *
     * @return void
     */
    public function monitor()
    {
        $taskName = input::get("taskName");
        $filename = 'common/files/monitor'.$taskName.'.txt';
        if (file_exists($filename)) {
            $data = file_get_contents($filename);
            echo str_replace("\n", "<br/>", $data);
        }

    }//end monitor()


    /**
     * 执行任务
     *
     * @return mixed
     */
    public function runTask()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');

        $taskName  = input::get("taskName");
        $tracePath = input::get("tracePath");
        $startTime = input::get("startTime");
        $type      = input::get("type");
        $ctrFlag   = input::get("ctrFlag");
        $user      = Auth::user()->user;

        Task::where('taskName', $taskName)->update(['status'=>'ongoing','startTime'=>$startTime]);
        if ($type == "ctrsystem" && $ctrFlag == "ctrFull") {
            $type = "ctrfullsystem";
            $command = "common/sh/start_mongs.sh ".$taskName." ".$tracePath." ".$type." /data/webdata/ctr/".$user;
        } else {
            $command = "common/sh/start_mongs.sh ".$taskName." ".$tracePath." ".$type;
        }
        exec($command);

        $command1 = "sudo common/sh/parameterPrint.sh ".$taskName;
        exec($command1);
        $row = Task::where('taskName', $taskName)->first()->toArray();
        if ($row['status'] == 'abort') {
            $return['status'] = "abort";
            $return['row']    = array(
                                 'taskName'   => $row['taskName'],
                                 'status'     => $row['status'],
                                 'startTime'  => $row['startTime'],
                                 'endTime'    => $row['endTime'],
                                 'tracePath'  => $row['tracePath'],
                                 'owner'      => $row['owner'],
                                 'createTime' => $row['createTime'],
                                 'type'       => $row['type'],
                                );
            return json_encode($return);
        } else {
            $endTime = date('y-m-d H:i:s', (time() + 8 * 3600));
            Task::where('taskName', $taskName)->update(['status'=>'complete','endTime'=>$endTime]);
            $return['status'] = "true";
            $return['row']    = array(
                                 'taskName'   => $row['taskName'],
                                 'status'     => 'complete',
                                 'startTime'  => $row['startTime'],
                                 'endTime'    => $endTime,
                                 'tracePath'  => $row['tracePath'],
                                 'owner'      => $row['owner'],
                                 'createTime' => $row['createTime'],
                                 'type'       => $row['type'],
                                );
            return json_encode($return);
        }//end if

    }//end runTask()


    /**
     * 中断任务
     *
     * @return mixed
     */
    public function stopTask()
    {
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getDB('mongs', 'mongs');
        $taskName = input::get("taskName");
        $endTime  = date('y-m-d H:i:s', (time() + 8 * 3600));
        Task::where('taskName', $taskName)->update(['status'=>'abort','endTime'=>$endTime]);
        $row = Task::where('taskName', $taskName)->first()->toArray();
        system("common/sh/stop_monitor.sh ".$taskName);
        $return['status'] = "abort";
        $return['row']    = array(
                             'taskName'   => $row['taskName'],
                             'status'     => "abort",
                             'startTime'  => $row['startTime'],
                             'endTime'    => $endTime,
                             'tracePath'  => $row['tracePath'],
                             'owner'      => $row['owner'],
                             'createTime' => $row['createTime'],
                             'type'       => $row['type'],
                            );
        return json_encode($return);

    }//end stopTask()


    /**
     * 导出解码配置文件
     *
     * @return string
     */
    public function exportFile()
    {
        $tracePath = input::get("tracePath");
        $file      = scandir($tracePath);
        if ($file) {
            system("mkdir ".$tracePath."/"."out/");
            krsort($file);
            foreach ($file as $value) {
                if ($value != "." && $value != ".." && $value != "out") {
                    system("/opt/ltetools/ltng-decoder -f ".$tracePath."/".$value."  -t /opt/ltetools/L14B/ > ".$tracePath."/"."out/".$value.".txt");
                }
            }

            exec("tar -czvf ".basename($tracePath).".tar.gz ".$tracePath."/"."out/"."*.txt");
        }

        return basename($tracePath).".tar.gz";

    }//end exportFile()


    /**
     * 上传本地文件
     *
     * @return string
     */
    public function uploadFile()
    {
        $text    = input::get("text");
        $date    = input::get("date");
        $id      = input::get("id");
        $dirName = $text."_".$date;
        if ($text == "CTRFULL") {
            $text = "CTR";
        }

        $text       = strtolower($text);
        $filename   = $_FILES[$id]['tmp_name'];
        $new_folder = "/data/trace/".$text."/".$dirName;
        if (!is_dir($new_folder)) {
            mkdir($new_folder, 0777);
            chmod($new_folder, 0777);
        }

        $result = move_uploaded_file($filename, $new_folder."/".$_FILES[$id]['name']);
        if ($result) {
            return $dirName;
        } else {
            return "error";
        }

    }//end uploadFile()

    /**
     * 根据类型全称转换成简称
     *
     * @return string
     */
    public function switchType($fullType){
        $type = '';
        switch ($fullType) {
            case 'parameter':
                $type = 'kget';
                break;
            case 'ctrsystem':
                $type = 'ctr';
                break;
            case 'cdrsystem':
                $type = 'cdr';
                break;
            case 'ebmsystem':
                $type = 'ebm';
                break;
            case 'pcapsystem':
                $type = 'pcap';
                break;
        }
        return $type;
    }

}//end class
