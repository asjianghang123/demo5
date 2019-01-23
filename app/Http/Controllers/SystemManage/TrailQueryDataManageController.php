<?php 

/**
     * TrailQueryDataManageController.php
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
use App\Models\Mongs\TrailQuery;
/**
 * 告警管理
 * Class TrailQueryDataManageController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class TrailQueryDataManageController extends Controller
{
    /**
     * 获得轨迹数据
     *
     * @return void
     */
    public function getData()
    {
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        // $items = array();
        $items = TrailQuery::paginate($rows)->toArray();
        $result = array();
        $result["total"] = $items['total'];
        $result['records'] = $items['data'];
        return json_encode($result);

    }//end getData()

     /**
      *导出轨迹数据 
      *
      */
     public function downloadFile()
     {
        $row = TrailQuery::get()->toArray();
        $result=array();

        $filename="common/files/轨迹数据管理_".date('YmdHis').".csv";
        $items=array();
        foreach ($row as $key => $value) {
           array_push($items, $value);
        }
        $result['text'] = 'eventTime,date_id,hour_id,city,imsi,cell,longitudeBD,latitudeBD,longitude,latitude,dir';
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
     * 导入轨迹数据
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

        TrailQuery::query()->delete();

        $sql = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE trailQuery character set GBK FIELDS terminated by ',' LINES TERMINATED BY '\n' IGNORE 1 LINES(eventTime,date_id,hour_id,city,imsi,cell,longitude,latitude,dir)";
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
