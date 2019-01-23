<?php 

/**
     * AlarmController.php
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
use App\Models\Mongs\AlarmInfo;
/**
 * 告警管理
 * Class AlarmController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AlarmController extends Controller
{
    /**
     * 获得告警信息
     *
     * @return void
     */
    public function getAlarm()
    {
        $items = array();
        $row = AlarmInfo::query()->get();
        foreach ($row as $val) {
            array_push($items, $val);
        }

        $result         = array();
        $result['text'] = 'id,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments';
        $result['rows'] = $items;
        return json_encode($result);

    }//end getAlarm()

     /**
      *导出告警数据 
      *
      */
     public function downloadFile()
     {
        $row = AlarmInfo::get()->sortBy('id')->toArray();
        $result=array();

        $filename="common/files/告警管理_".date('YmdHis').".csv";
        $items=array();
        foreach ($row as $key => $value) {
           array_push($items, $value);
        }
        $result['text'] = 'id,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments';
        $result['rows']=$items;
        $result['total']  = count($items);
        $result['result'] = 'true';
        $this->resultToCSV2($result, $filename);
        $result['filename']=$filename;

        echo json_encode($result);
     }



      /**
     * 写入CSV文件
     *
     * @param array  $result   Baseline模板内容
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()



    /**
     * 更新Baseline模板
     *
     * @return void
     */
    public function uploadFile()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');

        $filename=$_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }
        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }
        move_uploaded_file($filename, "common/files/".$_FILES['fileImport']['name']);
        setlocale(LC_ALL, null);


        $handle = fopen("common/files/".$_FILES['fileImport']['name'], 'r');
        $result = $this->inputCsv($handle);
        $len_result = count($result);
        if ($len_result == 0||$len_result==1) {
            echo 'lenError';
            exit;
        }
          $filename = "common/files/".$_FILES['fileImport']['name'];

        AlarmInfo::query()->delete();

        $sql = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE AlarmInfo character set utf8 FIELDS terminated by ',' LINES TERMINATED BY '\n' IGNORE 1 LINES(id,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments)";
        $query = $db -> exec($sql);
       
        if ($query) {
            echo "true";
        } else {
            echo 'false';
        }

    }//end uploadFile()


    /**
     * 读取CSV文件
     *
     * @param mixed $handle CSV文件句柄
     *
     * @return string array
     */
    protected function inputCsv($handle)
    {
        $out = array();
        $n   = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }

            $n++;
        }

        return $out;

    }//end inputCsv()

}
