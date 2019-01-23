<?php

/**
 * CsvZipDownloadController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * CSV文件压缩
 * Class CsvZipDownloadController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CsvZipDownloadController extends Controller
{


    /**
     * 压缩CSV文件
     *
     * @return array|string
     */
    public function getZipFile()
    {
        $fileDir  = Input::get('fileName');
        $filesize = filesize($fileDir);
        if (is_dir($fileDir) || $filesize >= 10000000) {
            $fileDir  = explode('/', $fileDir);
            $fileName = $fileDir[(count($fileDir) - 1)];
            $dir      = "";
            for ($i = 0; $i < (count($fileDir) - 1); $i++) {
                $dir = $dir.$fileDir[$i]."/";
            }

            $dirTo = "common/files/";
            exec('tar -C '.$dir.' -cvzf '.$dirTo.$fileName.'.tar.gz '.$fileName);
            return $dirTo.$fileName.'.tar.gz';
        } else {
            return $fileDir;
        }

    }//end getZipFile()


}//end class
