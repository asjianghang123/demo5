<?php
/**
 * Created by PhpStorm.
 * User: ericsson
 * Date: 2018/4/24
 * Time: 10:57
 */

namespace App\Jobs\NewSitesChecker;


use App\Models\Kget\EUtranCellRelation;
use App\Models\Kget\EUtranCellRelation_2;
use App\Models\Kget\GeranCellRelation;
use App\Models\Kget\GeranCellRelation_2;
use App\Models\Kget\ParaCheckBaseline;
use App\Models\SiteCheck\ManagedElement;
use App\Models\SiteCheck\SiteStatus;
use App\Models\Mongs\Task;
use App\Http\Controllers\Common\DataBaseConnection;
use DateTime;
use Config;
trait ParameterChecker
{
    /**
     * 邻区相关检查
     * @param $enbId
     */
    public static function doParameterCheck($enbId)
    {
      
        self::checkKget($enbId);
        self::checkBaseLine($enbId);
        self::checkNeighbor($enbId);
        self::check2GNeighbor($enbId);
    }

    /**
     * 检查kGET是否生成
     */
    public static function checkKget($enbId)
    {
        $check_item = Config('sitecheck.kget.kget_exist');
        // var_dump($check_item);
        //获得最新kget

        $kget = Task::where('type', 'parameter')->where('taskName', 'like', 'kget1_____')->orderBy('startTime', 'desc')->get(['taskName'])->first();
   
        config(["database.connections.kget.database" => $kget->taskName]);
        //检查kget是否生成
        // exit;
    
        $exists = ManagedElement::where('meContext', $enbId)->exists();
        $builder = SiteStatus::where('enbId', $enbId)->where('check_item', $check_item);
        //判断检查条目是否存在

        // 如果记录存在，则更新该记录
        // update site_status set status = true and days = days + 1 where enbId = xxx and check_item='kget文件未生成'
        // $days = $exists ? $item->days + 1 : 1;
        if ($builder->exists()) {
            $item = $builder->get()->first();
            $days = $exists ? 0 : $item->days + 1;
            $item->status = !$exists;
            $item->days = $days;
            $item->check_detail= $exists? "":"请联系genius开发人员，添加基站的IP至提取kget的IpList";
            $item->save();
            return;
        }
        // insert into site_status (endId, check_item, status, days) values (xxx, 'kget文件未生成', true, 1)
        // 如果记录不存在，则插入一条新的检查记录
        $item = new SiteStatus();
        $item->enbId = $enbId;
        $item->check_item = $check_item;
        $item->status = true;
        $item->up_time = new DateTime();
        $item->days = 1;
        $item->check_detail=$exists? "":"请联系genius开发人员，添加基站的IP至提取kget的IpList";
        $item->save();
        return;

    }

    /**
     * 检查baseLine
     */
    public static function checkBaseLine($enbId)
    {
        $db = new DataBaseConnection();

        $kget = $db->getKgetTime();

        config(["database.connections.kget.database" => $kget]);
        config(["database.connections.kget.database" => "kget180611"]);

        $exists = ParaCheckBaseline::where('meContext',$enbId)->get(['recommendedValue','realValue'])->toArray();
        $str = "";
        if($exists){
            for($i=0;$i<count($exists);$i++){
                $str.="参数".$i.":建议值:".$exists[$i]['recommendedValue'].",实际值:".$exists[$i]['realValue'].".\n";
            }

            $str = rtrim($str,'.');
        }

        $builder = SiteStatus::where('enbId', $enbId)->where('check_item', "baseline检查不一致");
        //判断检查条目是否存在
  
        // 如果记录存在，则更新该记录
        // update site_status set status = true and days = days + 1 where enbId = xxx and check_item='baseline检查不一致'
        if ($builder->exists()) {
            $item = $builder->get()->first();
            $days = $exists ? $item->days + 1: 0 ;
            $item->status = !!$exists;
            $item->days = $days;
            $item->check_detail = $exists?"$str":"";
            $item->save();
            return;
        }

        // 如果记录不存在，则插入一条新的检查记录
        // insert into site_status (endId, check_item, status, days) values (xxx, 'baseline检查不一致', true, 1)
        $item = new SiteStatus();
        $item->enbId = $enbId;
        $item->check_item = "baseline检查不一致";
        $item->status = true;
        $item->up_time = new DateTime();
        $item->days = 1;
        $item->check_detail = $exists?"$str":"";
        $item->save();
        return;

    }

    /**
     * 检查邻区定义
     */
    public static function checkNeighbor($enbId)
    {
        $db = new DataBaseConnection();

        $kget = $db->getKgetTime();

        config(["database.connections.kget.database" => $kget]);

        $result1=EUtranCellRelation::where('mecontext',$enbId)->exists();
        $result2=EUtranCellRelation_2::where('mecontext',$enbId)->exists();
      
        $builder = SiteStatus::where('enbId', $enbId)->where('check_item', "无4G邻区");
        //判断检查条目是否存在
  
        // 如果记录存在，则更新该记录
        // update site_status set status = true and days = days + 1 where enbId = xxx and check_item='无4G邻区'
        if ($builder->exists()) {
            $item = $builder->get()->first();
            $days = ($result1||$result2) ? 0 : $item->days + 1;
            $item->status = !($result1||$result2);
            $item->days = $days;
            $item->save();
            return;
        }
        // 如果记录不存在，则插入一条新的检查记录
        // insert into site_status (endId, check_item, status, days) values (xxx, '无4G邻区', true, 1)
        $item = new SiteStatus();
        $item->enbId = $enbId;
        $item->check_item = "无4G邻区";
        $item->status = true;
        $item->up_time = new DateTime();
        $item->days = 1;
        $item->save();
        return;

        // TODO
    }

    /**
     * 检查2G邻区定义
     */
    public static function check2GNeighbor($enbId)
    {
        $db = new DataBaseConnection();

        $kget = $db->getKgetTime();

        config(["database.connections.kget.database" => $kget]);

        $result1=GeranCellRelation::where('mecontext',$enbId)->exists();
        $result2=GeranCellRelation_2::where('mecontext',$enbId)->exists();
        $builder = SiteStatus::where('enbId', $enbId)->where('check_item', "无2G邻区");
        //判断检查条目是否存在
  
        // 如果记录存在，则更新该记录
        // update site_status set status = true and days = days + 1 where enbId = xxx and check_item='无2G邻区'
        if ($builder->exists()) {
            $item = $builder->get()->first();
            $days = ($result1||$result2) ? 0 : $item->days + 1;
            $item->status = !($result1||$result2);
            $item->days = $days;
            $item->save();
            return;
        }
        // 如果记录不存在，则插入一条新的检查记录
        // insert into site_status (endId, check_item, status, days) values (xxx, '无2G邻区', true, 1)
        $item = new SiteStatus();
        $item->enbId = $enbId;
        $item->check_item = "无2G邻区";
        $item->status = true;
        $item->up_time = new DateTime();
        $item->days = 1;
        $item->save();
        return;
        // TODO
    }

}