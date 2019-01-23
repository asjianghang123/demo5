<?php
/**
 * Created by PhpStorm.
 * User: ericsson
 * Date: 2018/4/24
 * Time: 11:04
 */

namespace App\Jobs\NewSitesChecker;
use PDO;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Models\Kget\ENodeBFunction;
use App\Models\SiteCheck\SiteStatus;
use App\Models\Mongs\TraceServerInfo;
use App\Models\Mongs\FtpServerInfo;
use Illuminate\Support\Facades\Storage;
use DateTime;
use Config;
trait MrrChecker
{

    /**
     * MRR相关检查.
     */
    public static function doMrrCheck($enbId)
    {   

        self::checkMrrFiles($enbId);
     
        // $this->checkMrrKpi($enbId);
    }

    /**
     * 检查MRR文件是否生成.
     *
     * @param $enbId string 站点ID
     */
    private static function checkMrrFiles($meContext)
    {
        $db = new DataBaseConnection();

        $kget = $db->getKgetTime();

        config(["database.connections.kget.database" => $kget]);
        $result=ENodeBFunction::where('mecontext',$meContext)->limit('1')->get(['eNBId'])->toArray();
        if($result){
            $eNBId= $result[0]['eNBId'];
        }else{
            return;
        }
        $rows =TraceServerInfo::where("city","suzhou")->where("type","mr")->get()->toArray();
        // $rows =FtpServerInfo::where("city","changzhou")->where("type","ctr")->get()->toArray();

   

        // $fileDir= "/data/trace/mro/backup/2018051114/100.93.252.109/";
        // date_default_timezone_set('PRC'); //设置中国时区 
        $dates = date("YmdH",strtotime("-1 hour"));
        // print_r($fileDir);//mro/backup/2018051114/100.93.252.109
        // $fileDir= "mro/backup/".$dates;
        // $fileDir = str_replace("/data/trace/", "", $fileDir);
             // $fileDir = $rows[0]['fileDir'];
        // $ftpHost     = "10.39.244.229";
        $ftpHost = $rows[0]['ipAddress'];
        $ftpUserName = $rows[0]['ftpUserName'];
        $ftpPassword = $rows[0]['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $ftpHost);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);

        $MR = array("mrs","mro","mre");


        foreach ($MR as $k => $v) {
            $fileDir= $v."/backup/".$dates;
            $files = Storage::disk('ftp')->allFiles($fileDir);
            // print_r(count($files));exit;
            foreach ($files as $file) {
                if(strpos($file, $eNBId)){
                     $builder = SiteStatus::where('enbId', $meContext)->where('check_item', "未生成MR文件");
                        if ($builder->exists()) {
                                $item = $builder->get()->first();
                                $days = 0 ;
                                $item->status = false;
                                $item->days = $days;
                                $item->save();
                                return;
                            }

                }

            }

        }

        // print_r($meContext);

        // 如果记录不存在，则插入一条新的检查记录
        // insert into site_status (endId, check_item, status, days) values (xxx, '未生成MR文件', true, 1)
        $builder = SiteStatus::where('enbId', $meContext)->where('check_item', "未生成MR文件");
        if ($builder->exists()) {
            $item = $builder->get()->first();
            $days =  $item->days + 1;
            $item->status = true;
            $item->days = $days;
            $item->save();
                return;
            }
        $item = new SiteStatus();
        $item->enbId = $meContext;
        $item->check_item = "未生成MR文件";
        $item->status = true;
        $item->up_time = new DateTime();
        $item->days = 1;
        $item->save();
        return;
    }

    /**
     * 检查MRR指标.
     *
     * @param $enbId string 站点ID
     */
    private function checkMrrKpi($enbId)
    {

    }

}