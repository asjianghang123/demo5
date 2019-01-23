<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use App\Models\SCHEMATA;
use Illuminate\Support\Facades\Input;
use PDO;

class WilliamToolController extends Controller
{
    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getTasks()
    {
         $tasks = SCHEMATA::select('SCHEMA_NAME')->where('SCHEMA_NAME', 'like', 'kget1_____')->where('SCHEMA_NAME', 'not like', 'kgetpart%')->where('SCHEMA_NAME', 'not like', 'kget_External%')->orderBy('SCHEMA_NAME', 'desc')->get()->toArray();
        $items = array();
        foreach ($tasks as $task) {
            $items[] = array("text"=>$task['SCHEMA_NAME'],"id"=>$task['SCHEMA_NAME']);
        }
        return response()->json($items);//需要通过response返回响应数据
    }




    /**
     * 获取Lte_carrier数据
     * @DateTime 2018-04-25
     * @return   [type]     [description]
     */
    public function getCarrierData()
    {
    	$kget = Input::get("kget");
    	$dbc = new DataBaseConnection();
        $db  = $dbc->getDb('kget',$kget);
   	
   		$title ="ECGI  CEll  XDD  eNBId  cellId  TAC  CI  EARFCN  PCI  physicalLayerSubCellId  physicalLayerCellIdGroup  maximumTransmissionPower  operationalState  Font_size  rachRootSequence";

    	$sql = "SELECT
				concat('4600-' ,eNBid,'-',cellId) AS ECGI,
				cellName AS CELL,
				duplexMode AS XDD,
				eNBid,
				cellId,
				tac AS TAC,
				cellId+eNBid as CI,
				earfcn AS EARFCN,
				pci AS PCI,
				physicalLayerSubCellId,
				physicalLayerCellIdGroup,
				maximumTransmissionPower,
				operationalState,
				'5' as 'Font_size',
				rachRootSequence
			FROM
				(
					SELECT
						b.EUtranCellTDDId,
						b.cellId,
						c.eNBId,
						b.physicalLayerSubCellId,
						b.physicalLayerCellIdGroup,
						maximumTransmissionPower,
						operationalState,
						rachRootSequence,
						a.cellName,
						a.duplexMode,
						a.earfcn,
						a.pci,
						a.tac
					FROM
						$kget.EUtranCellTDD b
					LEFT JOIN mongs.siteLte a ON b.EUtranCellTDDId = a.cellName
					LEFT JOIN $kget.ENodeBFunction c on b.meContext = c.meContext
					WHERE
						a.cellName IS NOT NULL and c.eNBId is not null 
							GROUP BY cellName limit 10
				) a ";

		$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		$result = array();

		foreach ($row as $key => $value) {
			$result['rows'][]=$value;
		}

		$sql=str_replace("TDD", "FDD", $sql);
		//FDD小区
		$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		foreach ($row as $key => $value) {

			$result['rows'][]=$value;
		}
		$result['text']='ECGI,CEll,XDD,eNBId,cellId,TAC,CI,EARFCN,PCI,physicalLayerSubCellId,physicalLayerCellIdGroup,maximumTransmissionPower,operationalState,Font_size,rachRootSequence';
		return json_encode($result);
    }

    /**
     * 获取Lte_Neighbor信息
     * @DateTime 2018-04-22
     * @return   string
     */
    public function getNeighborData()
    {	
    	error_reporting(0);
    	$kget = Input::get("kget");
    	$dbc  = new DataBaseConnection();
        $db   = $dbc->getDb('kget',$kget);
        $sql1  = "SELECT
			 	b.EUtranCellTDD,
				a.ecgi,
				b.EUtranCellRelationId
				FROM
				$kget.EUtranCellRelation b	
				LEFT JOIN mongs.siteLte a ON  b.EUtranCellTDD=a.cellName "  ;
		$sql2  = "SELECT
			 	b.EUtranCellFDD,
				a.ecgi,
				b.EUtranCellRelationId
				FROM
				$kget.EUtranCellRelation_2 b	
				LEFT JOIN mongs.siteLte a ON  b.EUtranCellFDD=a.cellName "  ;
		$sql1 = "SELECT
					b.EUtranCellTDD,
					a.ecgi,
					CASE length(b.EUtranCellRelationId) - length(
						REPLACE (
							b.EUtranCellRelationId,
							'-',
							''
						)
					)
				WHEN '2' THEN
					b.EUtranCellRelationId
				ELSE
					(
						SELECT
							ecgi
						FROM
							mongs.siteLte
						WHERE
							cellName = substring(
								b.EUtranCellRelationId,
								instr(b.EUtranCellRelationId, '-') + 1,
								LENGTH(b.EUtranCellRelationId)
							) limit 1
					) 
				END as neighbor
				FROM
					$kget.EUtranCellRelation b
				LEFT JOIN mongs.siteLte a ON b.EUtranCellTDD = a.cellName limit 300 ";
		$sql2 = "SELECT
					b.EUtranCellFDD,
					a.ecgi,
					CASE length(b.EUtranCellRelationId) - length(
						REPLACE (
							b.EUtranCellRelationId,
							'-',
							''
						)
					)
				WHEN '2' THEN
					b.EUtranCellRelationId
				ELSE
					(
						SELECT
							ecgi
						FROM
							mongs.siteLte
						WHERE
							cellName = substring(
								b.EUtranCellRelationId,
								instr(b.EUtranCellRelationId, '-') + 1,
								LENGTH(b.EUtranCellRelationId)
							) limit 1
					) 
				END as neighbor
				FROM
					$kget.EUtranCellRelation_2 b
				LEFT JOIN mongs.siteLte a ON b.EUtranCellFDD = a.cellName where a.ecgi is not null  limit 300";


	 	$row2  = $db->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
		$data = array();
		
		foreach($row2 as $v){
			if($v['ecgi']){
				$data[$v['ecgi']].=$v['neighbor'].'  ';		
			}
		}
		// exit;
		array_pop($data);
		$results = array();

		// print_r($data);exit;
			$num =$this->getItem($data);
			 	// print_r($data['4600-161163-130']);
			 	// print_r($array);
			 	// exit;
		 	$title="ECGI	";

		 	$results['text']="ECGI,";
		 	for($i=1;$i<=$num+1;$i++){
		 		$results['text'].="lte_NCELL$i,";
		 	}
		 	// print_r($results['text']);exit;
		 	$results['text']=rtrim($results['text'],',');
		 	foreach ($data as $key => $value) {
		 		$temp = array();
		 		$temp = explode("  ", trim($value));
		 		array_unshift($temp,$key);
		 		$results['rows'][]= $temp ;
		 		// print_r($value);
		 	}

			return json_encode($results);

    }//end getNeighborData()


    function getItem($array) {
        $index = 0;
        foreach ($array as $k => $v) {
            if (strlen($array[$index]) < strlen($v))
                $index = $k;
        }
        return count(explode("  ", $array[$index]));
    }

    public function importCarrierData()
    {
    	$kget = Input::get("kget");
    	$dbc = new DataBaseConnection();
        $db  = $dbc->getDb('kget',$kget);
   		
        $fileName = "common/files/MAPWILL_LTE_Carrier_data_$kget.txt";
		if(file_exists($fileName)){
			unlink($fileName);
		}


   		$title ="ECGI  CEll  XDD  eNBId  cellId  TAC  CI  EARFCN  PCI  physicalLayerSubCellId  physicalLayerCellIdGroup  maximumTransmissionPower  operationalState  Font_size  rachRootSequence";

		$handel = fopen($fileName, "a+");
	 	fwrite($handel,$title."\r\n");
    	$sql = "SELECT
				concat('4600-' ,eNBid,'-',cellId) AS ECGI,
				cellName AS CELL,
				duplexMode AS XDD,
				eNBid,
				cellId,
				tac AS TAC,
				cellId+eNBid as CI,
				earfcn AS EARFCN,
				pci AS PCI,
				physicalLayerSubCellId,
				physicalLayerCellIdGroup,
				maximumTransmissionPower,
				operationalState,
				'5' as 'Font_size',
				rachRootSequence
			FROM
				(
					SELECT
						b.EUtranCellTDDId,
						b.cellId,
						c.eNBId,
						b.physicalLayerSubCellId,
						b.physicalLayerCellIdGroup,
						maximumTransmissionPower,
						operationalState,
						rachRootSequence,
						a.cellName,
						a.duplexMode,
						a.earfcn,
						a.pci,
						a.tac
					FROM
						$kget.EUtranCellTDD b
					LEFT JOIN mongs.siteLte a ON b.EUtranCellTDDId = a.cellName
					LEFT JOIN $kget.ENodeBFunction c on b.meContext = c.meContext
					WHERE
						a.cellName IS NOT NULL and c.eNBId is not null 
							GROUP BY cellName
				) a ";

		$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		$result = array();

		foreach ($row as $key => $value) {
			$st = implode("	", $value);
 			fwrite($handel, $st."\r\n");
		}

		$sql=str_replace("TDD", "FDD", $sql);
		//FDD小区
		$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		foreach ($row as $key => $value) {
			$st = implode("	", $value);
 			fwrite($handel, $st."\r\n");
		}
		fclose($handel);
	
		$result['filename'] = $fileName;
		return json_encode($result);

    }
    /**
     * 导出Neighbor数据
     * @DateTime 2018-05-08
     * @return   [type]     [description]
     */
    public function importNeighborData()
    {
    	error_reporting(0);
    	$kget = Input::get("kget");
    	$dbc  = new DataBaseConnection();
        $db   = $dbc->getDb('kget',$kget);

		$sql1 = "SELECT
					b.EUtranCellTDD,
					a.ecgi,
					CASE length(b.EUtranCellRelationId) - length(
						REPLACE (
							b.EUtranCellRelationId,
							'-',
							''
						)
					)
				WHEN '2' THEN
					b.EUtranCellRelationId
				ELSE
					(
						SELECT
							ecgi
						FROM
							mongs.siteLte
						WHERE
							cellName = substring(
								b.EUtranCellRelationId,
								instr(b.EUtranCellRelationId, '-') + 1,
								LENGTH(b.EUtranCellRelationId)
							) limit 1
					) 
				END as neighbor
				FROM
					$kget.EUtranCellRelation b
				LEFT JOIN mongs.siteLte a ON b.EUtranCellTDD = a.cellName  ";
		$sql2 = "SELECT
					b.EUtranCellFDD,
					a.ecgi,
					CASE length(b.EUtranCellRelationId) - length(
						REPLACE (
							b.EUtranCellRelationId,
							'-',
							''
						)
					)
				WHEN '2' THEN
					b.EUtranCellRelationId
				ELSE
					(
						SELECT
							ecgi
						FROM
							mongs.siteLte
						WHERE
							cellName = substring(
								b.EUtranCellRelationId,
								instr(b.EUtranCellRelationId, '-') + 1,
								LENGTH(b.EUtranCellRelationId)
							) limit 1
					) 
				END as neighbor
				FROM
					$kget.EUtranCellRelation_2 b
				LEFT JOIN mongs.siteLte a ON b.EUtranCellFDD = a.cellName where a.ecgi is not null ";

		// echo $sql1;exit;
	 	$row1  = $db->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
		// print_r($row2);
		// exit;
		$data = array();
		foreach($row1 as $v){
			if($v['ecgi']){
				$data[$v['ecgi']].=$v['neighbor'].'  ';		
			}
		}


	 	$row2  = $db->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
		foreach($row2 as $v){
			if($v['ecgi']){
				$data[$v['ecgi']].=$v['neighbor'].'  ';		
			}
		}

		$results = array();


		$num =$this->getItem($data);
	
		$title="ECGI	";

	 	$results['text']="ECGI,";
	 	for($i=1;$i<=$num+1;$i++){
	 		$title .= "lte_NCELL$i	";
	 	}

	 	$title = rtrim($title,'');

		 $fileName = "common/files/MAPWILL_LTE_Neighbor_data_$kget.txt";
		 	if(file_exists($fileName)){
				unlink($fileName);	
			}
		$handle = fopen($fileName,'a');
		
			fwrite($handle,$title."\r\n");

		 	foreach ($data as $key => $value) {
		 		fwrite($handle,ltrim($key)."  ");
		 		fwrite($handle,$value."\r\n");
		 		fwrite($handle,"\r\n");
		 	}
		$results['filename'] = $fileName;
		return json_encode($results);
    }	


}
