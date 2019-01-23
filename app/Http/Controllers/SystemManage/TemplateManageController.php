<?php 

/**
 * TemplateManageController.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
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
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\Template;
use App\Models\Mongs\KpiTempCommon;
use App\Models\Mongs\Kpiformula;

/**
 * 模板管理
 * Class TemplateManageController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class TemplateManageController extends Controller
{   

    public function getAllTypes()
    {
        $row    = KpiTempCommon::orderBy('id', 'asc')->groupBy('type')->get()->toArray();

        $array= array();
        foreach ($row as $val)
        {
            $type = '{"text":"' . $val['type'] . '","value":"' . $val['type']. '"}';
            array_push($array, $type);
        }
       return response()->json($array);
    }

    /**
     * 文件上传
     *@return void
     */
    public function uplodeFile()
    {    
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');

        $filename=$_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo 'emptyError';
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
 
        $sql='';

        KpiTempCommon::query()->delete(); 
        
        $sql = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE kpiTemplateCommon character set utf8 FIELDS terminated by ',' LINES TERMINATED BY '\n' IGNORE 1 LINES(id,type,dataSource,templateName,kpiName,kpiFormula,kpiPrecision)";
        
      

       
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

    /**
     *导出流控指标数据 
     *
     *@return string
     */
    public function downloadFile()
    {   
        $type       = empty(input::get('type'))?'FlowQuery':input::get('type');
        if ($type=='FlowQuery') {
            $row    = KpiTempCommon::get()->sortBy('id')->toArray();
            $tmp_filename='专项指标查询';
        }
        
        $result     = array();

        $filename   ="common/files/".$tmp_filename."_".date('YmdHis').".csv";
        $items=array();
        foreach ($row as $key => $value) {
           array_push($items, $value);
        }
        $result['text']   = 'id,type,dataSource,templateName,kpiName,kpiFormula,kpiPrecision';
        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';
        $this->resultToCSV2($result, $filename);
        $result['filename']= $filename;

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
            $item = array();
            foreach ($row as $r) {
                array_push($item, mb_convert_encoding($r, 'GBK'));
            }
            fputcsv($fp, $item);
        }

        fclose($fp);

    }//end resultToCSV2()

    /**
     * 获取数据
     *
     *@return array
     */
    public function getManageDate()
    {
        $items  = array();
        $type   = input::get('type');

        if ($type=='') {
            $row    = KpiTempCommon::query()->get();
        } else {

            $row =KpiTempCommon::whereIn('type', $type)->get();
        }
        foreach ($row as $val) {
            array_push($items, $val);
        }

        $result         = array();
        $result['text']   = 'id,type,dataSource,templateName,kpiName,kpiFormula,kpiPrecision';
        $result['rows'] = $items;
        echo json_encode($result);
    }// end getManageDate()

    /**
     * 删除列
     */
    public function deleteData()
    {
        $id     = input::get('id');
        $result = KpiTempCommon::where('id', $id)->delete();
        if ($result) {
            echo true;
        } else {
            echo false;
        }
    }//end deleteData()

    /**
     * 新建和修改模板
     */
    public function updateManage()
    {
        $id        = input::get("formulaId");
        $type      = input::get('type');
        $dataSource= input::get('dataSource');
        $tempName  = input::get('templateName');  
        $name      = input::get("name");
        $precision = input::get("precision");
        $formula   = input::get("formula");
        if ($id) {
                $res = KpiTempCommon::where('id', $id)->update([
                        'type'=>$type,
                        'dataSource'=>$dataSource,
                        'templateName'=>$tempName,
                        'kpiName'=>$name,
                        'kpiFormula'=>$formula,
                        'kpiPrecision'=>$precision
                    ]);
        }else {
            $newKpi = new KpiTempCommon;
            $newKpi->type=$type;
            $newKpi->dataSource=$dataSource;
            $newKpi->templateName=$tempName;
            $newKpi->kpiName = $name;
            $newKpi->kpiFormula = $formula;
            $newKpi->kpiPrecision = $precision;
            $res = $newKpi->save();
        }

        if ($res) {
            echo true;
        } else {
            echo false;
        }
    }//end updateManage()
}
