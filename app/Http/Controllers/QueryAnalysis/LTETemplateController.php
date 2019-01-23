<?php

/**
 * LTETemplateController.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\Users;
use App\Models\Mongs\Template;
use App\Models\Mongs\Kpiformula;
use App\Models\Mongs\KpiTempCommon;

/**
 * LTE模板管理
 * Class LTETemplateController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LTETemplateController extends GetTreeData
{


    /**
     * 获得模板列表
     *
     * @return void
     */
    public function getTreeData()
    {
        $login_user = Auth::user()->user;
        // if ($login_user == "admin") {
        //     $users      = Template::distinct('user')->get(['user']);
        // } else {
        //     $users      = Template::whereIn("user",[$login_user,"admin"])->distinct('user')->get(['user']);
        // }
        $users      = Template::distinct('user')->get(['user']);
        $arrUser    = array();
        $items      = array();
        $itArr      = array();

        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = Template::where('user', $userStr)->orderBy('templateName', 'asc')->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
            }

            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }
        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = Users::where('user', $user)->first(); 
                if ($nameCNSql) {
                    $itArr[$i]['text'] = $nameCNSql->name;
                }
            }
        }

        return response()->json($itArr);

    }//end getTreeData()


    /**
     * 检索指定模板
     *
     * @return mixed
     */
    public function searchLTETreeData()
    {
        $inputData = Input::get('inputData');
        $login_user = Auth::user()->user;
        // if ($login_user == "admin") {
        //     $users      = Template::distinct('user')->get(['user']);
        // } else {
        //     $users      = Template::whereIn("user",[$login_user,"admin"])->distinct('user')->get(['user']);
        // }
        $users      = Template::distinct('user')->get(['user']);
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $conn = Template::where('user', $userStr)->where('templateName', 'like', "%".$inputData."%");
            if ($conn->exists()){
                $templateNames = $conn->orderBy('templateName', 'asc')->get()->toArray();
                foreach ($templateNames as $templateName) {
                    $temp['text'] = $templateName['templateName'];
                    $temp['id']   = $templateName['id'];
                    array_push($arrUser, $temp);
                }
                if ($userStr == "admin") {
                    $items["text"] = "通用模板";
                } else if ($userStr == "system") {
                    $items["text"] = "系统模板";
                } else {
                    $nameCNSql         = Users::where('user', $userStr)->first(); 
                    if ($nameCNSql) {
                        $items['text'] = $nameCNSql->name;
                    }
                }

                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        }//end foreach

        return response()->json($itArr);

    }//end searchLTETreeData()


    /**
     * 获得指标列表
     *
     * @return void
     */
    public function getElementTree()
    {
        $templateId = input::get('templateId');
        $elementId = Template::where('id', $templateId)->first();
        echo json_encode($elementId);

    }//end getElementTree()


    /**
     * 获得指标显示名
     *
     * @return void
     */
    public function getKpiNamebyId()
    {
        $idarr = explode(',', input::get("id"));
        $items = array();
        foreach ($idarr as $id) {
            $row = Kpiformula::where('id', $id)->first();
            if ($row) {
                $row = $row->toArray();
                $data['text'] = $row['kpiName'];
                $data['id']   = $row['id'];
                $data['user'] = $row['user'];
                array_push($items, $data);
            }
        }

        echo json_encode($items);

    }//end getKpiNamebyId()


    /**
     * 获得指标模板信息集合
     *
     * @return void
     */
    public function getTreeTemplate()
    {
        $login_user = Auth::user()->user;
        $rsUsersArr = Kpiformula::distinct('user')->get(['user'])->toArray();
        // 筛选所有user
        $idNum = 1;
        $items = array();
        // 存储最终结果
        $result = array();
        foreach ($rsUsersArr as $row) {
            //0609
            /*if ($login_user != 'admin' && $row['user'] != "admin" && $row['user'] != $login_user) {
                continue;
            }*/
            $result['id'] = $idNum--;
            if ($row['user'] == "admin") {
                $result['kpiName'] = "通用模板";
            } else {
                $user = Users::where('user',$row['user'])->first();
                if ($user) {
                    $result['kpiName'] = $user->name;
                } else {
                    $result['kpiName'] = $row['user'];
                }
            }

            $result['state'] = 'closed';
            $children        = array();
            $user            = $row['user'];
            $resArr = Kpiformula::where('user', $user)->orderBy('kpiName', 'asc')->get()->toArray();
            foreach ($resArr as $rs) {
                $kpiName      = $rs['kpiName'];
                $kpiFormula   = $rs['kpiFormula'];
                $kpiPrecision = $rs['kpiPrecision'];
                $kpiId        = $rs['id'];
                array_push($children, array("id" => $kpiId, "kpiName" => $kpiName, "kpiFormula" => $kpiFormula, "kpiPrecision" => $kpiPrecision));
            }
            $result['children'] = $children;
            array_push($items, $result);
        }//end foreach

        echo json_encode($items);

    }//end getTreeTemplate()


    /**
     * 更新指标公式
     *
     * @return void
     */
    public function updateFormula()
    {
        $id        = input::get("formulaId");
        $name      = input::get("name");
        $precision = input::get("precision");
        $formula   = input::get("formula");
        $user      = Auth::user()->user;

        $result['error'] = $this->checkFormula($formula);

        if(!$result['error']){
            if ($id) {
                if ($user == "admin") {
                    $res = Kpiformula::where('id', $id)->update([
                            'kpiName'=>$name,
                            'kpiFormula'=>$formula,
                            'kpiPrecision'=>$precision
                        ]);
                } else {
                    $res = Kpiformula::where('id', $id)->where('user', $user)->update([
                            'kpiName'=>$name,
                            'kpiFormula'=>$formula,
                            'kpiPrecision'=>$precision
                        ]);
                }

            } else {
                $newKpiformula = new Kpiformula;
                $newKpiformula->kpiName = $name;
                $newKpiformula->kpiFormula = $formula;
                $newKpiformula->kpiPrecision = $precision;
                $newKpiformula->user = $user;
                $res = $newKpiformula->save();
            }

            if($res){
                 $result["error"]="";
            }else{
                 $result["error"]="创建失败";
            }
            return json_encode($result);
        }else{
            $result['error']="check:".$result['error'];
            return json_encode($result);
        }

    }//end updateFormula()

    /**
     * 检查公式
     * @DateTime 2018-04-13
     * @param    
     * @return   array()
     */
    public function checkFormula($kpiformula){
        $kpiformula    = strtolower($kpiformula);
        $pattern       = "/[\(\)\+\*-\/]/";
        $columns       = preg_split($pattern, $kpiformula);

        foreach ($columns as $key => $value) {
           if(is_numeric($value)||$value==''||in_array($value, array('','max','min','avg'))){
            unset($columns[$key]);
           }
        }
        // print_r($columns);exit;
         $error        = "";
        foreach ($columns as $column) {
            if(strpos($column,'pm')!==false){

                if(!$this->checkCounter(trim($column))){
                        $error.=$column.",";
                    };
            }
        }//end foreach
        if($error){
            $error=rtrim($error,",");
        }
        return $error;
    }

    /**
     * 检查counter格式和counter值是否存在
     * @DateTime 2018-04-23
     * @param    [type]     $counter [description]
     * @return   string
     */
    protected function checkCounter($counter){
        $result=$this->loadCounter();
        if($result){
            if(array_key_exists('1',explode("_",$counter))){
                $array = array();
                $array = explode("_", $counter);
                if(array_key_exists($array[0], $result)){
                      if(strlen($array[1])==strlen(ltrim($array[1]))){
                        return true;   
                      }
                    }else{
                        return false;
                    }
                }
        if(array_key_exists($counter, $result)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    

   
    }

    /**
     * 加载本地counter值 
     * @DateTime 2018-04-23
     * @return   array
     */
    protected function loadCounter(){
        $result = array();
        $lines  = file("common/txt/Counters.txt");
        foreach ($lines as $line) {
            $pair = explode("=", $line);
            $result[strtolower($pair[0])] = $pair[1];
        }
        $lines  = file("common/txt/Counters_FDD.txt");
        foreach ($lines as $line) {
            $pair = explode("=", $line);
            $result[strtolower($pair[0])] = $pair[1];
        }
        return $result;
    }
    /**
     * 删除指标公式
     *
     * @return void
     */
    public function deleteFormula()
    {
        $id   = input::get("id");
        $user = Auth::user()->user;
        if ($user == "admin") {
            $res = Kpiformula::destroy($id);
        } else {
            $res = Kpiformula::where('id', $id)->where('user', $user)->delete();
        }

        if (!$res) {
            echo false;
            return;
        }

        // 删除后更新template表中相关内容
        $resTem = Template::get()->toArray();
        $row      = [];
        $i        = 0;

        foreach ($resTem as $rowTem) {
            $row[$i++] = $rowTem['elementId'];
        }
        $rowTem   = [];
        $rowTem_1 = [];
        $k        = 0;
        for ($i = 0; $i < count($row); $i++) {
            $rowTem[$i] = explode(',', $row[$i]);
            for ($j = 0; $j < count($rowTem[$i]); $j++) {
                if ($rowTem[$i][$j] == $id) {
                    $rowTem_1[$k] = implode(',', $rowTem[$i]);
                    $k++;
                }
            }
        }

        $finalRow_0 = [];
        $finalRow_1 = [];
        $finalRow   = [];
        for ($i = 0; $i < count($rowTem_1); $i++) {
            $m = 0;
            $finalRow_0[$i] = explode(',', $rowTem_1[$i]);
            $finalRow_1[$i] = array();
            for ($j = 0; $j < count($finalRow_0[$i]); $j++) {
                if ($finalRow_0[$i][$j] != $id) {
                    $finalRow_1[$i][$m] = $finalRow_0[$i][$j];
                    $m++;
                }
            }

            $finalRow[$i] = implode(',', $finalRow_1[$i]);
        }

        $sql_update = [];
        for ($i = 0; $i < count($finalRow); $i++) {
            $old            = $rowTem_1[$i];
            $new            = $finalRow[$i];
            Template::where('elementId', $old)->update(['elementId'=>$new]);
        }

        echo true;

    }//end deleteFormula()


    /**
     * 检索指定指标公式
     *
     * @return void
     */
    public function searchTreeTemplate()
    {
        $formula = input::get("formula");
        $formula = "%".$formula."%";
        $table   = "kpiformula";
        $rsUsersArr = Kpiformula::distinct('user')->get(['user'])->toArray();
        $idNum = 1;
        $items = array();
        $result = array();
        // 存储用户结果
        foreach ($rsUsersArr as $row) {
            $result['id'] = $idNum++;
            if ($row['user'] == "admin") {
                $result['kpiName'] = "通用模板";
            } else {
                $user = Users::where('user', $row['user'])->first();
                if ($user) {
                    $result['kpiName'] = $user->name;
                } else {
                    $result['kpiName'] = $row['user'];
                }
            }

            $result['state'] = 'closed';
            $children        = array();
            $user            = $row['user'];

            $res = Kpiformula::where('user', $user)
                        ->where(function($query) use($formula) {
                            $query->where('kpiFormula', 'like', $formula)
                                ->orWhere('kpiName', 'like', $formula);
                        });
            if ($res->exists()) {
                $resArr =    $res->orderBy('kpiName', 'asc')->get()->toArray();
                foreach ($resArr as $value) {
                    $kpiName      = $value['kpiName'];
                    $kpiFormula   = $value['kpiFormula'];
                    $kpiPrecision = $value['kpiPrecision'];
                    $kpiId        = $value['id'];
                    array_push($children, array("id" => $kpiId, "kpiName" => $kpiName, "kpiFormula" => $kpiFormula, "kpiPrecision" => $kpiPrecision));                          
                }
                $result['children'] = $children;
                array_push($items, $result);
                
            }
        }//end foreach

        echo json_encode($items);

    }//end searchTreeTemplate()


    /**
     * 更新指标公式
     *
     * @return void
     */
    public function updateElement()
    {
        $id   = input::get('id');
        $ids  = input::get('ids');
        $user = Auth::user()->user;
        if ($user == "admin") {
            $exeres = Template::where('id', $id)->update(['elementId'=>$ids]);
        } else {
            $exeres = Template::where('id', $id)->where('user', $user)->update(['elementId'=>$ids]);
        }
        if ($exeres) {
            echo true;
        } else {
            echo false;
        }

    }//end updateElement()



     /**
     * 更新模板名称
     *
     * @return void
     */
    public function updateNewMode()
    {
       
        $newModeName = input::get("newname");
        $oldModeName = input::get("oldname");
        $user = Auth::user()->user;
        $result = Template::where('templateName', $newModeName)->where('user', $user)->count();
        if ($result != 0) {
            echo "名称已有";
            return;
        }
        if ($user == "admin") {
            $exeres = Template::where('templateName', $oldModeName)->update(['templateName'=>$newModeName]);
        } else {
            $exeres = Template::where('templateName', $oldModeName)->where('user', $user)->update(['templateName'=>$newModeName]);
        }
        if ($exeres) {
            echo true;
        } else {
            echo false;
        }

    }//end updateMode()
    
    /**
     * 新增模板
     *
     * @return void
     */
    public function addMode()
    {
        $templateName = input::get('modeName');
        $description  = input::get('description');

        $user = Auth::user()->user;
        $result = Template::where('templateName', $templateName)->where('user', $user)->count();
        // $result = preg_replace('/\s+/', '', $result);
        if ($result != 0) {
            echo "名称已有";
            return;
        }

        $newTemplate = new Template;
        $newTemplate->templateName = $templateName;
        $newTemplate->description = $description;
        $newTemplate->user = $user;
        $res =$newTemplate->save();

        if ($res) {
            echo true;
        } else {
            echo false;
        }

    }//end addMode()


    /**
     * 删除模板
     *
     * @return void
     */
    public function deleteMode()
    {
        $id = input::get('id');
        $user = Auth::user()->user;
        if ($user == "admin") {
            $res = Template::destroy($id);
            if ($res) {
                echo "1";
            } else {
                echo "2";
            }
        } else {
            $res = Template::where('id', $id)->where('user', $user)->delete();
            if ($res) {
                echo "1";
            } else {
                echo "3";
            }
        }

    }//end deleteMode()


    /**
     * 复制模板
     *
     * @return void
     */
    public function copyMode()
    {
        $user      = Auth::user()->user;
        $id           = input::get("copyId");
        $templateName = input::get('modeName_copy');
        $description  = input::get('description');
        $result = Template::where('templateName', $templateName)->where('user', $user)->count();
        if ($result != 0) {
            echo "名称已有";
            return;
        }

        $elementId = Template::where('id', $id)->first()->elementId;
        $newTemplate = new Template;
        $newTemplate->templateName = $templateName;
        $newTemplate->elementId = $elementId;
        $newTemplate->description = $description;
        $newTemplate->user = $user;
        $res =$newTemplate->save();

        if ($res) {
            echo true;
        } else {
            echo false;
        }

    }//end copyMode()
	
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
        
		 $sql = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE kpiformula character set utf8 FIELDS terminated by ',' LINES TERMINATED BY '\n' IGNORE 1 LINES(kpiName,kpiFormula,kpiPrecision,format,user)";
		$user = Auth::user()->toArray(); 
        $query = $db -> exec($sql);
		
		//插入用户名
		$id_max = DB::table('kpiformula')->max('id');
		$nums = $id_max - $len_result + 2;  
		DB::table('kpiformula')->whereBetween('id', [$nums, $id_max])->update([
                'user' => $user['user'],
            ]);
		     
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
      *导出该用户的kpiformula_test表数据
      *
      */
     public function downloadFile()
     {
		$user = Auth::user()->toArray();
        $row = kpiFormula::get()->where('user',$user['user'])->sortBy('id')->toArray();
		//error_log(print_r($row,1),3,'./logs13333.txt');
        $result=array();

        $filename="common/files/公式_".date('YmdHis').".csv";
        $items=array();
        foreach ($row as $key => $value) {
           array_push($items, $value);
        }
        $result['text'] = 'id,kpiName,user,kpiFormula,kpiPrecision,format';
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
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK', 'UTF-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            $temp = "";
            foreach ($row as $key => $value) {
                $temp .= $value.",";
            }
            $temp = substr($temp, 0, -1);
            fwrite($fp, mb_convert_encoding($temp. "\n", 'GBK', 'UTF-8'));
        }
        fclose($fp);

    }//end resultToCSV2()


}//end class
