<?php

/**
 * FileUtil.php
 *
 * @category Utils
 * @package  App\Http\Controllers\Utils
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\Utils;
use PDO;
/**
 * 文件工具类
 * Class FileUtil
 *
 * @category Utils
 * @package  App\Http\Controllers\Utils
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class FileUtil
{


    /**
     * 读入CSV文件
     *
     * @param string $fileName CSV文件名
     *
     * @return array
     */
    function parseFile($fileName)
    {
        $handle = fopen("common/files/".$fileName, 'r');
        $out    = array();
        $n      = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }

            $n++;
        }

        fclose($handle);
        // 关闭指针
        return $out;

    }//end parseFile()


    /**
     * 写入CSV文件
     *
     * @param array  $column   表头
     * @param array  $items    内容
     * @param string $fileName CSV文件名
     *
     * @return void
     */
    function resultToCSV2($column, $items, $fileName)
    {
        $csvContent = mb_convert_encoding($column."\n", 'GBK');
        $fp         = fopen($fileName, "w");
        fwrite($fp, $csvContent);
        foreach ($items as $row) {
            $newRow = array();
            foreach ($row as $key => $value) {
                $newRow[$key] = mb_convert_encoding($value, 'GBK');
            }

            fputcsv($fp, $newRow);
        }

        fclose($fp);

    }//end resultToCSV2()
    /**
     * 大数据写入CSV文件
     *
     * @param array  $rs       结果对象
     * @param string $fileName CSV文件名
     *
     * @return int   $totalCount      数据总数
     */
    function resultToCSV($rs, $fileName)
    {
        $fp = fopen($fileName, "w");
        $totalCount = 0;
        while ($row=$rs->fetch(PDO::FETCH_ASSOC)) {
            $totalCount = $totalCount + 1;
            if ($totalCount == 1) {
                $column = implode(",", array_keys($row));
                $csvContent = mb_convert_encoding($column . "\n", 'GBK');
                fwrite($fp, $csvContent);
                $newRow = array();
                foreach ($row as $key => $value) {
                    $newRow[$key] = mb_convert_encoding($value, 'GBK');
                }
                fputcsv($fp, $newRow);
            } else {
                $newRow = array();
                foreach ($row as $key => $value) {
                    $newRow[$key] = mb_convert_encoding($value, 'GBK');
                }
                fputcsv($fp, $newRow);
            }
        }
        fclose($fp);
        return $totalCount;

    }//end resultToCSV()


}//end class
