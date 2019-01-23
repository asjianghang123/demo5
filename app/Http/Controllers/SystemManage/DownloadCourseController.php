<?php

/**
 * DownloadCourseController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * 操作视频下载
 * Class DownloadCourseController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class DownloadCourseController extends Controller
{


    /**
     * 获得文档列表
     *
     * @return void
     */
    public function getDoc()
    {
        $array = array();
        $dir   = "/data/trace/Doc/guide";
        $files = scandir($dir);
        krsort($files);
        $items = array();
        foreach ($files as $file) {
            if ($file == '.') {
                continue;
            } else if ($file == '..') {
                continue;
            } else {
                $items['label'] = $file;
                $items['value'] = $file;
                $size           = round(((filesize($dir."/".$file) / 1024) / 1024), 2);
                $items['size']  = $size."MB";
                array_push($array, $items);
            }
        }

        echo json_encode($array);

    }//end getDoc()


    /**
     * 获取Video文件列表
     *
     * @return void
     */
    public function getVideo()
    {
        $array = array();
        $dir   = "/data/trace/Doc/video";
        $files = scandir($dir);
        krsort($files);
        $items = array();
        foreach ($files as $file) {
            if ($file == '.') {
                continue;
            } else if ($file == '..') {
                continue;
            } else {
                $items['label'] = $file;
                $items['value'] = $file;
                $size           = round(((filesize($dir."/".$file) / 1024) / 1024), 2);
                $items['size']  = $size."MB";
                array_push($array, $items);
            }
        }

        echo json_encode($array);

    }//end getVideo()


}//end class
