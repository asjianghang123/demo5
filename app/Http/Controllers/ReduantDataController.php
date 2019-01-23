<?php

/**
 * 
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use PDO;
use DB;
use Config;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\SiteLte;
class ReduantDataController extends Controller
{

    /**
     * 下载模板
     * @author zhangguoli
     * @DateTime 2018-03-29
     * @return   [type]     [description]
     */
    public function downloadTemplateFile(){


    	$fileName        = "common/files/删除小区信息_".date('YmdHis').".csv";
    	// $text  = "Site Name,Cell Name,ENBID,Cell ID";
    	$result['text']  = 'Site Name,Cell Name,ENBID,Cell ID';
    
        $items=array();
      
        $result['rows']=$items;
        $result['total']  = count($items);
        $result['result'] = 'true';
        $this->resultToCSV2($result, $fileName);
        $result['fileName']=$fileName;

        // echo json_encode($result);
        return $result;

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
        if($result['rows']){

	        foreach ($result['rows'] as $row) {
	            fputcsv($fp, $row);
	        }
        }

        fclose($fp);

    }//end resultToCSV2()



    public function uploadFile(){
        $filename=$_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo 'lenError';
            exit;
        }
        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }
        move_uploaded_file($filename, "common/files/".$_FILES['fileImport']['name']);
        setlocale(LC_ALL, null);


        $handle = fopen("common/files/".$_FILES['fileImport']['name'], 'r');
        $cell = array();
        while ($data = fgetcsv($handle,1000)){
            $cell[] = $data[1];
        }
        if($cell){
             if (count($cell) == 0||count($cell)==1) {
                echo 'lenError';
                exit;
            }

            array_shift($cell);
            $result['cellInfo']= $this->loadCellInfo($cell);
            $result['Info']  = $this->load4GInfo($cell);
            return json_encode($result);
        }else{
            echo 'lenError';
            exit;
        }
        

        
        
    }


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



    protected function loadCellInfo($site){

        $result = array();
        $dateStart = date('Y-m-d',strtotime("-8 day"));
        $dateEnd   = date('Y-m-d',strtotime("-1 day"));
        $str = '';
        // foreach ($site as $value) {
        //     $str.="'".$value."',";
        // }
        // print_r($site);
        $row=SiteLte::select('cellName','subNetwork','city','duplexMode')->whereIN('cellName',$site)->get();
        $ziwang=array();
        foreach ($row as $key => $value) {
            if($value['duplexMode']&&$value['subNetwork']){
                $ziwang[$value['duplexMode']][$value['subNetwork']][]=$value['cellName']; 
            }
            // $ziwang[$value['city']][]=$value['subNe
        }
        $result['rows']=array();
        if(!empty($ziwang)){
            foreach($ziwang as $key=>$value){
                foreach($value as $ks => $vs){
                    if($key =="TDD"){
                        $row = Databaseconns::select('host','port','dbName','userName','password')->where('subNetwork','like','%'.$ks.'%')->get()->toArray();
                    }else if($key=="FDD"){
                        $row = Databaseconns::select('host','port','dbName','userName','password')->where('subNetworkFDD','like','%'.$ks.'%')->get()->toArray();

                    }else{
                        continue;
                    }
                        $cellStr ='';
                        foreach ($vs as $v) {
                                $cellStr .="'".$v."',";

                        }
                        $cellStr =rtrim($cellStr,',');
                        if($row){
                            $host = $row[0]["host"];
                            $port = $row[0]["port"];
                            $dbName = $row[0]["dbName"];
                            $userName = $row[0]["userName"];
                            $password = $row[0]["password"];
                            $locationDim = "cell";
                            $pmDbDSN = "dblib:host=" . $host . ":" . $port . ";dbname=" . $dbName;

                            $pmDB = new PDO($pmDbDSN, $userName, $password);

                            $sql="SELECT
                                    AGG_TABLE0. DAY,
                                    AGG_TABLE0.subNet,
                                    AGG_TABLE0.site,
                                    AGG_TABLE0.location,
                                    cast(agg0 AS DECIMAL(18, 0)) AS kpi0,
                                    cast(
                                        pmRrcConnLevSum / pmRrcConnLevSamp AS DECIMAL (18, 2)
                                    ) AS kpi1,
                                    cast(
                                        pmPdcpVolUlDrb / (1024 * 8) AS DECIMAL (18, 2)
                                    ) AS kpi2,
                                    cast(
                                        pmPdcpVolDlDrb / (1024 * 8) AS DECIMAL (18, 2)
                                    ) AS kpi3
                                FROM
                                    (
                                        SELECT
                                            CONVERT (CHAR(10), date_id) AS DAY,
                                            substring(
                                                SN,
                                                charindex ('=', substring(SN, 32, 25)) + 32,
                                                charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1
                                            ) AS subNet,
                                            substring(
                                                substring(
                                                    SN,
                                                    charindex (',', substring(SN, 32, 25)) + 32
                                                ),
                                                11,
                                                25
                                            ) AS site,
                                            EutranCell".$key." AS location,
                                            max(pmRrcConnMax) AS 'agg0',
                                            sum(pmRrcConnLevSum) AS 'pmRrcConnLevSum',
                                            sum(pmRrcConnLevSamp) AS 'pmRrcConnLevSamp',
                                            sum(pmPdcpVolUlDrb) AS 'pmPdcpVolUlDrb',
                                            sum(pmPdcpVolDlDrb) AS 'pmPdcpVolDlDrb'
                                        FROM
                                            dc.DC_E_ERBS_EUTRANCELL".$key."_day
                                        WHERE
                                            date_id between '$dateStart'
                                        AND '$dateEnd'
                                        AND substring(
                                            SN,
                                            charindex ('=', substring(SN, 32, 25)) + 32,
                                            charindex (',', substring(SN, 32, 25)) - charindex ('=', substring(SN, 32, 25)) - 1
                                        ) IN (
                                            '$ks'
                                        )
                                        AND EutranCell".$key." IN ($cellStr)
                                        GROUP BY
                                            date_id,
                                            SN,
                                            location
                                    ) AS AGG_TABLE0
                                ORDER BY
                                    AGG_TABLE0.location,
                                    AGG_TABLE0. DAY";
                                    // exit;
                                    // 
                                try {
                                    $resultSet = $pmDB->query($sql, PDO::FETCH_ASSOC);  
                                      if($resultSet){
                                              foreach ($resultSet as $k => $v) {
                                                $result['rows'][]=$v;
                                            }
                                        }
                            
                                } catch (Exception $e) {
                                        continue;
                                }
                              
                        }
                }
           }
        }
     

        $result['text'] = 'day,subNetwork,site,location,最大RRC连接用户数,RRCConnected状态下的平均用户数,上行平均流量／天,下行平均流量／天';
        $filename        = "common/files/删除小区一周的业务量_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename']=$filename;

        return $result;

    }
    protected function load4GInfo($site){

        $datas = array();
        $date=date('Ymd');
        $dbc = new DataBaseConnection();
        $kget = $dbc->getKgetTime();
        $db  = $dbc->getDb('kget',$kget);

        foreach ($site as  $siteValue) {
            $row=SiteLte::select('duplexMode')->where('cellName',$siteValue)->get()->toArray();
            if(!$row){
               continue;
            }else if($row[0]['duplexMode']=="TDD"){
                $type1  =   "TDD";
                $type2  =   "FDD";
                $table1 =   "EUtranCellRelation";
                $table2 =   "EUtranCellRelation_2";

            }else if($row[0]['duplexMode']=="FDD"){
                $type1  =    "FDD";
                $type2  =    "TDD";
                $table1 ="EUtranCellRelation_2";
                $table2 ="EUtranCellRelation";
            }else{
                continue;
            }
            $sql ="SELECT
                b.meContext,
                c.eNBId,
                b.EUtranCell".$type1.",
                d.cellId,
                d.physicalLayerCellIdGroup,
                d.physicalLayerCellId,
                b.EUtranCellRelationId
            FROM
                $kget.$table1 b
            LEFT JOIN mongs.siteLte a ON b.EUtranCell".$type1." = a.cellName
            LEFT JOIN $kget.ENodeBFunction c on b.meContext = c.meContext
            LEFT JOIN $kget.EUtranCell".$type1." d on d.EUtranCell".$type1."Id = a.cellName
            WHERE
                b.EUtranCell".$type1." = '".$siteValue."'";
            $row  = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if($row){

                foreach($row as $v){ 
                    if(substr($v['EUtranCellRelationId'],0,4)=='4600'){
                            $sql="select cellName from mongs.siteLte where ecgi='".$v['EUtranCellRelationId']."'";      
                            $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                            $neightcell = $result['cellName'];

                    }else{
                            $neightcell = explode("-", $v['EUtranCellRelationId'])[1];
                    }
                    $sql ="SELECT
                            b.meContext,
                            c.eNBId,
                            b.EUtranCell".$type1.",
                            a.earfcn AS ErelationARFCN,
                            d.cellId,
                            d.physicalLayerCellIdGroup,
                            d.physicalLayerCellId,
                            '4600' as plmnId,
                            a.ecgi,
                            d.tac,
                            b.ipAddr as IP
                        FROM
                            $kget.$table1 b
                        LEFT JOIN mongs.siteLte a ON b.EUtranCell".$type1." = a.cellName
                        LEFT JOIN $kget.ENodeBFunction c ON b.meContext = c.meContext
                        LEFT JOIN $kget.EUtranCell".$type1." d ON d.EUtranCell".$type1."Id = a.cellName
                        WHERE
                            b.EUtranCell".$type1." = '".$neightcell."' limit 1";
                            $neighResult = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


                            $newDate = array();

                            $newDate[]=$v['meContext'];
                            $newDate[]=$v['eNBId'];
                            $newDate[]=$v['EUtranCell'.$type1];
                            $newDate[]=$v['cellId'];
                            $newDate[]=$v['physicalLayerCellIdGroup'];
                            $newDate[]=$v['physicalLayerCellId'];
                            if($neighResult){

                            $newDate[]=$neighResult[0]['meContext'];
                            $newDate[]=$neighResult[0]['eNBId'];
                            $newDate[]=$neighResult[0]['EUtranCell'.$type1];
                            $newDate[]=$neighResult[0]['ErelationARFCN'];
                            $newDate[]=$neighResult[0]['cellId'];
                            $newDate[]=$neighResult[0]['physicalLayerCellIdGroup'];
                            $newDate[]=$neighResult[0]['physicalLayerCellId'];
                            $newDate[]=$neighResult[0]['plmnId'];
                            $newDate[]=$neighResult[0]['tac'];
                            $newDate[]=$neighResult[0]['IP'];
                        }else{
                            //TDD-FDD邻区
                          $sql ="SELECT
                            b.meContext,
                            c.eNBId,
                            b.EUtranCell".$type2.",
                            a.earfcn AS ErelationARFCN,
                            d.cellId,
                            d.physicalLayerCellIdGroup,
                            d.physicalLayerCellId,
                            '4600' as plmnId,
                            a.ecgi,
                            d.tac,
                            b.ipAddr as IP
                        FROM
                            $kget.$table2 b
                        LEFT JOIN mongs.siteLte a ON b.EUtranCell".$type2." = a.cellName
                        LEFT JOIN $kget.ENodeBFunction c ON b.meContext = c.meContext
                        LEFT JOIN $kget.EUtranCell".$type2." d ON d.EUtranCell".$type2."Id = a.cellName
                        WHERE
                            b.EUtranCell".$type2." = '".$neightcell."' limit 1";
                         $neighResult = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                            if($neighResult){
                                $newDate[]=$neighResult[0]['meContext'];
                                $newDate[]=$neighResult[0]['eNBId'];
                                $newDate[]=$neighResult[0]['EUtranCell'.$type2];
                                $newDate[]=$neighResult[0]['ErelationARFCN'];
                                $newDate[]=$neighResult[0]['cellId'];
                                $newDate[]=$neighResult[0]['physicalLayerCellIdGroup'];
                                $newDate[]=$neighResult[0]['physicalLayerCellId'];
                                $newDate[]=$neighResult[0]['plmnId'];
                                $newDate[]=$neighResult[0]['tac'];
                                $newDate[]=$neighResult[0]['IP'];
                            }else{

                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';
                                $newDate[]='';

                            }
                        }

                            $datas['rows'][]=$newDate;

                    // }

                }
            }else{
                 $datas['rows'][]=array();
            }





        }
        $datas['text'] = "Serving Site ID,Serving eNBId,Serving Cell Name,Serving cellId,Serving PhysicalLayerCellIdGroup,Serving physicalLayerSubCellId,Neighbor Site ID,Neighbor eNBId,Neighbor Cell Name,ErelationARFCN,Neighbor cellId,Neighbor PhysicalLayerCellIdGroup,Neighbor physicalLayerSubCellId,plmnId,tac,IP";
        $filename        = "common/files/删除小区4G邻区信息_".date('YmdHis').".csv";
        $this->resultToCSV2($datas, $filename);
        $datas['filename']=$filename;
       return $datas;

    }

}
