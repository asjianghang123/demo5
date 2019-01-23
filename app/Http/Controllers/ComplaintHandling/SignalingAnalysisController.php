<?php

/**
 * SignalingAnalysisController.php
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ComplaintHandling;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use MongoClient;
use MongoId;

/**
 * 信令分析
 * Class SignalingAnalysisController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SignalingAnalysisController extends Controller
{

    /**
     * 获得图表数据
     *
     * @return string
     */
    public function getChartData()
    {
        $m = new MongoClient();
        $db = $m->pcap;
        $dataBase = input::get("dataBase");
        $collection = $db->$dataBase;
        $count = $collection->count();
        if ($count == 0) {
            echo "false";
            return;
        }
        $ips = $collection->find()->fields(array("ip_src" => true, "ip_dst" => true));
        // 选择所有ip节点
        $nodes = [];
        foreach ($ips as $ip) {
            array_push($nodes, $ip["ip_src"]);
            array_push($nodes, $ip["ip_dst"]);
        }
        $nodes = array_unique($nodes);
        $nodes_new = [];
        foreach ($nodes as $n) {
            array_push($nodes_new, $n);
        }

        $cursor = $collection->find()->fields(array("date_id" => true, "ip_src" => true, "ip_dst" => true, "msg_name" => true))->sort(array('timestamp' => 1));
        // 迭代显示文档标题
        $items = [];
        foreach ($cursor as $document) {
            array_push($items, $document);
        }

        $result = [];
        $result["nodes"] = $nodes_new;
        $result["rows"] = $items;
        $result["nodes_length"] = count($nodes_new);
        return json_encode($result);

    }//end getChartData()


    /**
     * 获得信令详细
     *
     * @return string
     */
    public function showMessage()
    {
        $id = input::get("id");
        $m = new MongoClient();
        // 连接
        $db = $m->pcap;
        // 获取名称为 "test" 的数据库
        $dataBase = input::get("dataBase");
        $collection = $db->$dataBase;
        $cursor = $collection->findOne(array("_id" => new MongoId($id)), array("packet" => true));
        $items = $this->getFields($cursor["packet"]["proto"]);
        $result = [];
        $result["tree"] = $items;
        return json_encode($result);
    }//end showMessage()


    /**
     * 获得域名列表
     *
     * @param array $packet packet
     *
     * @return array 域名列表
     */
    protected function getFields($packet)
    {
        $tree = [];
        foreach ($packet as $p) {
            if (isset($p["showname"]) && $p["showname"] != "General information") {
                $showname = $p["showname"];
                $node = [];
                $node["name"] = $showname;
                if (isset($p["field"])) {
                    $children = $this->getFields($p["field"]);
                    $node["children"] = $children;
                }
                array_push($tree, $node);
            }
        }
        return $tree;
    }//end getFields()


    /**
     * 获得数据库名
     *
     * @return string
     */
    public function getDataBase()
    {
        $m = new MongoClient();
        // 连接
        $db = $m->pcap;
        // 获取名称为 "pcap" 的数据库
        $collections = $db->getCollectionNames();
        $items = array();
        foreach ($collections as $collection) {
            array_push($items, ["id" => $collection, "text" => $collection]);
        }

        return json_encode($items);

    }//end getDataBase()


}//end class
