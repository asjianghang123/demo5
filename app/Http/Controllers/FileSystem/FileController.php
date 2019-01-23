<?php

/**
 * FileController.php
 *
 * @category FileSystem
 * @package  App\Http\Controllers\FileSystem
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\FileSystem;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Common\DataBaseConnection;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


use App\Task;
use App\User;
use App\DatabaseConn;
use PDO;

/**
 * 文件工具集
 * Class FileController
 *
 * @category Exporter
 * @package  App\Http\Controllers\FileSystem
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class FileController extends Controller
{

    public function Query_uploadFile()
    {
        $filename = $_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo '请选择要导入的文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }

        move_uploaded_file($filename, "common/files/".$_FILES['fileImport']['name']);

        setlocale(LC_ALL, null);
        $files = file("common/files/".$_FILES['fileImport']['name']);
        foreach ($files as $txt) {
            print_r($txt);
        }

    }

    /**
     * 文件上传
     *
     * @return string 上传文件名
     */
    function uploadFile()
    {
        $fileName = $_FILES['fileImport']['tmp_name'];
        if (empty($fileName)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }

        move_uploaded_file($fileName, "common/files/".$_FILES['fileImport']['name']);
        return $_FILES['fileImport']['name'];

    }//end uploadFile()

    function majorActivities_uploadFile()
    {
        $fileName = $_FILES['majorActivities_fileImport']['tmp_name'];
        if (empty($fileName)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES['majorActivities_fileImport']['name'])) {
            unlink("common/files/".$_FILES['majorActivities_fileImport']['name']);
        }

        move_uploaded_file($fileName, "common/files/".$_FILES['majorActivities_fileImport']['name']);
        return $_FILES['majorActivities_fileImport']['name'];

    }//end uploadFile()

    function newUploadFile(){
        $fileElementId = Input::get("fileElementId");
        $fileName = $_FILES[$fileElementId]['tmp_name'];
        if (empty($fileName)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES[$fileElementId]['name'])) {
            unlink("common/files/".$_FILES[$fileElementId]['name']);
        }

        move_uploaded_file($fileName, "common/files/".$_FILES[$fileElementId]['name']);
        return $_FILES[$fileElementId]['name'];
    }// end newUploadFile
}//end class
