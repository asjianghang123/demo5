<?php
/**
 * TaskController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;
/**
 * 任务管理
 * Class TaskController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class TaskController extends Controller
{
    /**
     * 获取crontab文件内容
     *
     * @return void
     */
    public function openTaskFile()
    {
        //$sh = "sudo crontab -u root -l > common/txt/root.txt";//$path="/var/spool/cron/root"
            //echo $sh;
        $remoteIp = Input::get("remoteIp");
        $path = input::get("path");//mkdir -m 777 pc
        $sh = 'sudo ssh root@'.$remoteIp.' "cd /data;mkdir cronTemp"';
        //echo $sh;
        exec($sh);
        $sh = 'sudo ssh root@'.$remoteIp.' "crontab -u root -l > '.$path.'"';
        //echo $sh;
        exec($sh);
        $sh = 'sudo ssh root@'.$remoteIp.' "chmod -R 777 '.$path.'"';
        //echo $sh;
        exec($sh);
        $scp = "sudo scp -r root@".$remoteIp.":".$path." ".$path;
        //echo $scp;
        exec($scp);
        //chmod('/data/cronTemp/root.txt',0777);
        $myfile = fopen($path, "r") or die("Unable to open file!");
        // 输出单行直到 end-of-file
        while (!feof($myfile)) {
            echo fgets($myfile);
        }
        fclose($myfile);
    }//end openTaskFile()
    /**
     * 保存crontab文件内容
     *
     * @return void
     */
    public function saveTaskFile()
    {
        $remoteIp = Input::get("remoteIp");
        $path = Input::get("path");
        $myfile = fopen($path, "w") or die("Unable to open file!");
        // 输出单行直到 end-of-file
        $content = Input::get("content");
        
        //echo $content;
        fwrite($myfile, $content);
        fclose($myfile);
        $scp = "sudo scp -r ".$path." root@".$remoteIp.":".$path;//远程拷贝文件
        //echo $scp;
        exec($scp);
        $sh = 'sudo ssh root@'.$remoteIp.' "crontab -u root '.$path.'"';//登录到远程linux服务器上并执行脚本 244:mongs账户
        //echo $sh;
        exec($sh);
        //$sh = "sudo crontab -u lijian $path";//备用账户：lijian 账户：root
        //echo $sh;
        //exec($sh);
    }//end saveTaskFile()
}