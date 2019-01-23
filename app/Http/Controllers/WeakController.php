<?php

/**
 * WeakController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDO;
use Config;
use App\Models\Kget\ParaCheckBaseline;

/**
 * 短板概览
 * Class WeakController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WeakController extends Controller
{


    /**
     * Baseline检查-参数数量分布数据
     *
     * @return string
     */
    public function getBaselineParamNum()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db      = 'kget'.$yesDate;
        Config::set("database.connections.kget.database", $db);

        $dbc        = new DataBaseConnection();
        $rs_city    = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res        = $dbc->getCitySubNetCategories();
        $series     = array();
        foreach ($res as $items) {
            $arr        = array();
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
            $rs         = ParaCheckBaseline::where(function($query) {
                                                                    $query->where('category', 'A')
                                                                          ->orWhere('category', 'M');
                                                                    })->whereIn('subNetwork', $subNetworkArr)
                                                                    ->count();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }

        $data['category'] = $categories;
        $data['series']   = array();
        $data['series'][] = [
                             'name' => 'city',
                             'data' => $series,
                            ];
        return json_encode($data);

    }//end getBaselineParamNum()


    /**
     * Highcharts图形的category
     *
     * @param array $rs 查询结果
     *
     * @return array
     */
    public function getHighChartCategory($rs)
    {
        $categories = array();
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }

        return $categories;

    }//end getHighChartCategory()


    /**
     * Baseline检查-基站数量分布数据
     *
     * @return string
     */
    public function getBaselineBSNum()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate    = $date->format('ymd');
        $db         = 'kget'.$yesDate;
        Config::set("database.connections.kget.database", $db);
        $dbc        = new DataBaseConnection();
        $rs_city    = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res        = $dbc->getCitySubNetCategories();
        $series     = array();
        foreach ($res as $items) {
            $arr        = array();
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
            $rs         = ParaCheckBaseline::distinct('meContext')
                              ->where(function($query) {
                                $query->where('category', 'A')
                                    ->orWhere('category', 'M');
                            })->whereIn('subNetwork', $subNetworkArr)
                            ->count('meContext');
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }

        $data['category'] = $categories;
        $data['series']   = array();
        $data['series'][] = [
                             'name' => 'city',
                             'data' => $series,
                            ];
        return json_encode($data);

    }//end getBaselineBSNum()


    /**
     * 子网拼接 in 查询语句
     *
     * @param string $subNetwork 子网字串
     *
     * @return string
     */
    protected function reCombine($subNetwork)
    {

        $subNetArr  = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()


    /**
     * 一致性检查-基站数量分布数据
     *
     * @return string
     */
    public function getConsistencyParamNum()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate    = $date->format('ymd');
        $db         = 'kget'.$yesDate;
        $dbc        = new DataBaseConnection();
        $dbn        = $dbc->getDB('mongs', $db);
        $rs_city    = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res        = $dbc->getCitySubNetCategories();
        $series     = array();
        foreach ($res as $items) {
            $arr        = array();
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $sql        = "select sum(num) from (select count(*) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
                    select count(*) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToENB_ENBID_ipAddress where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToENB_ENBID_usedIpAddress where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToENB_IP where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToENB_X2Status where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToMme_S1_MMEGI where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToMme_S1_MME where subNetwork in (".$subNetwork.") union all
                    select count(*) num from TempTermPointToMme_S1_tac where subNetwork in (".$subNetwork.")
                    ) t";
            $rs         = $dbn->query($sql, PDO::FETCH_OBJ);
            $rs         = $rs->fetchColumn();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }//end foreach

        $data['category'] = $categories;
        $data['series']   = array();
        $data['series'][] = [
                             'name' => 'city',
                             'data' => $series,
                            ];
        return json_encode($data);

    }//end getConsistencyParamNum()


    /**
     * 一致性检查-基站数量分布数据
     *
     * @return string
     */
    public function getConsistencyBSNum()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate    = $date->format('ymd');
        $db         = 'kget'.$yesDate;
        $dbc        = new DataBaseConnection();
        $dbn        = $dbc->getDB('mongs', $db);
        $rs_city    = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res        = $dbc->getCitySubNetCategories();
        $series     = array();
        foreach ($res as $items) {
            $arr        = array();
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $sql        = "select sum(num) from (select count(distinct meContext) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
                    select count(distinct meContext) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToENB_ENBID_ipAddress where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToENB_ENBID_usedIpAddress where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToENB_IP where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToENB_X2Status where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToMme_S1_MMEGI where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToMme_S1_MME where subNetwork in (".$subNetwork.") union all
                    select count(distinct meContext) num from TempTermPointToMme_S1_tac where subNetwork in (".$subNetwork.")
                    ) t";
            $rs         = $dbn->query($sql, PDO::FETCH_OBJ);
            $rs         = $rs->fetchColumn();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }//end foreach

        $data['category'] = $categories;
        $data['series']   = array();
        $data['series'][] = [
                             'name' => 'city',
                             'data' => $series,
                            ];
        return json_encode($data);

    }//end getConsistencyBSNum()

}//end class
