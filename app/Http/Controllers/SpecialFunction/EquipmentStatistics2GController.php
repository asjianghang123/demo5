<?php

namespace App\Http\Controllers\SpecialFunction;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
* 
*/
class EquipmentStatistics2GController extends Controller
{
    
    public function getTasks()
    {
        $filter = '';
        $items = array();
        $i = 0;
        $data = 'CDD%';
        $tasks = DB::select('SELECT `SCHEMA_NAME` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` LIKE ? ORDER BY `SCHEMA_NAME` DESC;', [$data]);
        foreach ($tasks as $task) {
            $items[$i++] = '{"text":"' . $task->SCHEMA_NAME . '"}';
        }
        return response()->json($items);//需要通过response返回响应数据
    }

    public function getSituationDataHeader() {
        $dbname = Input::get('dataBase');

        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', $dbname);       
        $sql = "SELECT
            bsc AS '频段',
            cgi AS 'GSM升级到FDD',
            bcchno AS '说明',
            agblk AS '基站数',
            irc AS '小区数'
            
                FROM RLDEP
                LIMIT 1";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    public function getAllSituationData() {
        $column = "频段,GSM升级到FDD,说明,基站数,小区数";
        $dbname = Input::get('dataBase');
        $name = '现网设备情况'; 
        $fileName   = "files/".$dbname."_".$name."_".date('YmdHis').".csv";
        $dbname = Input::get('dataBase');
        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', $dbname);
        
        $sql = "SELECT
            CSYSTYPE AS '频段',
            UPGRATE_TYPE AS 'GSM升级到FDD',
            TYPE AS '说明',
            COUNT(DISTINCT SITE) AS '基站数',
            COUNT(CELL) AS '小区数'
            FROM
            (
                SELECT
                if(@sitename=LEFT(CELL,LENGTH(CELL)-1), null,@sitename) AS 'SITE',
                @sitename:=LEFT(CELL,LENGTH(CELL)-1),
                LEFT(CELL,LENGTH(CELL)-1) AS '',
                BSC,
                CELL,
                CSYSTYPE,
                DCHNO_COUNT,
                IFNULL(RBS_TYPE,'RBS2000') AS 'RBS_TYPE',
                RUS_TYPE,
                RUS_COUNT,
                CASE 
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND dchno_count<=8 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                ELSE '不能升级的射频单元型号' END AS 'UPGRATE_TYPE',
                CASE WHEN ISNULL(RBS_TYPE) THEN 'RBS2000'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6201+ rus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6201+ rus01*1（4<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6601+ rrus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6601+ rrus01*1（4<=载波数）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '宏站6201+ rus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '宏站6201+ rus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '分布式6601+ rrus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '分布式6601+ rrus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus12*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus12*1（7<=载波数<=8）'
                ELSE 'UNKNOWN' END AS 'TYPE'
                FROM RLDEP
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,
                    COUNT(DCHNO) AS 'DCHNO_COUNT'
                    FROM RLCFP
                    WHERE DCHNO<>''
                    GROUP BY BSC,CELL
                )t1 USING(BSC,CELL)
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,
                    COUNT(DISTINCT RUSERIALNO) AS 'RUS_COUNT',
                    CASE WHEN RULOGICALID like 'MCTR RUS%' THEN '6201' 
                        WHEN RULOGICALID like 'MCTR RRUS%' THEN '6601' 
                        ELSE 'RBS2000' END AS 'RBS_TYPE',
                    CASE WHEN RULOGICALID like 'MCTR RUS 01%' THEN 'RUS01' 
                        WHEN RULOGICALID like 'MCTR RUS 02%' THEN 'RUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 01%' THEN 'RRUS01' 
                        WHEN RULOGICALID like 'MCTR RRUS 02%' THEN 'RRUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 12%' THEN 'RRUS12' 
                        ELSE '2000' END AS 'RUS_TYPE'
                    FROM RXMFP
                    LEFT JOIN RXMOP USING(BSC,mo)
                    WHERE RULOGICALID LIKE 'MCTR%'
                    GROUP BY BSC,CELL
                    ORDER BY RUS_TYPE DESC
                )t2 USING(BSC,CELL)
                WHERE CELL NOT LIKE 'TEST%' AND CELL NOT LIKE 'YJ%'
                ORDER BY BSC,CELL
            )TT1
            GROUP BY CSYSTYPE,UPGRATE_TYPE,TYPE";
        //这段sql语句需要执行两次才能正确取到基站数的值，所以在这里先进行一次无意义的查询，以使基站数正常
        $rs  = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $res = $db->query($sql);
        if ($res) {
            $csvContent = mb_convert_encoding($column."\n", 'GBK');
            $fp         = fopen($fileName, "w");
            fwrite($fp, $csvContent);
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) { 
                $newRow = array();
                foreach ($row as $key => $value) {
                    $newRow[$key] = mb_convert_encoding($value, 'GBK');
                }
                fputcsv($fp, $newRow);
            }
            fclose($fp);
            $result['fileName'] = $fileName;
            $result['result']   = true;
        } else {
            $result['result'] = false;
        }
        return $result;
    }
    

        public function getSituationData() {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";
        $filter = '';

        $dbname = Input::get('dataBase');
        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', $dbname);

        $sql = "SELECT 
                    COUNT(*) totalCount
                FROM
                    (
                        SELECT
                CSYSTYPE AS '频段',
                UPGRATE_TYPE AS 'GSM升级到FDD',
                TYPE AS '说明',
                COUNT(DISTINCT SITE) AS '基站数',
                COUNT(CELL) AS '小区数'
                FROM
                (
                    SELECT
                    if(@sitename=LEFT(CELL,LENGTH(CELL)-1), null,@sitename) AS 'SITE',
                    @sitename:=LEFT(CELL,LENGTH(CELL)-1),
                    LEFT(CELL,LENGTH(CELL)-1) AS '',
                    BSC,
                    CELL,
                    CSYSTYPE,
                    DCHNO_COUNT,
                    IFNULL(RBS_TYPE,'RBS2000') AS 'RBS_TYPE',
                    RUS_TYPE,
                    RUS_COUNT,
                    CASE 
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND dchno_count<=8 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                    ELSE '不能升级的射频单元型号' END AS 'UPGRATE_TYPE',
                    CASE WHEN ISNULL(RBS_TYPE) THEN 'RBS2000'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6201+ rus01*1（0<=载波数<=3）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6201+ rus01*1（4<=载波数）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6601+ rrus01*1（0<=载波数<=3）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6601+ rrus01*1（4<=载波数）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*1（0<=载波数<=6）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus02*1（7<=载波数<=8）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*1（0<=载波数<=6）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*1（7<=载波数<=8）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus01*2 （0<=载波数<=6）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus01*2 （7<=载波数<=8）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*2 （0<=载波数<=6）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '宏站6201+ rus02*2 （7<=载波数<=8）'
                    WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '宏站6201+ rus02*2 （9<=载波数）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus01*2 （0<=载波数<=6）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '分布式6601+ rrus01*2 （7<=载波数<=8）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*2 （0<=载波数<=6）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*2 （7<=载波数<=8）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '分布式6601+ rrus02*2 （9<=载波数）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus12*1（0<=载波数<=6）'
                    WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus12*1（7<=载波数<=8）'
                    ELSE 'UNKNOWN' END AS 'TYPE'
                    FROM RLDEP
                    LEFT JOIN 
                    (
                        SELECT
                        BSC,CELL,
                        COUNT(DCHNO) AS 'DCHNO_COUNT'
                        FROM RLCFP
                        WHERE DCHNO<>''
                        GROUP BY BSC,CELL
                    )t1 USING(BSC,CELL)
                    LEFT JOIN 
                    (
                        SELECT
                        BSC,CELL,
                        COUNT(DISTINCT RUSERIALNO) AS 'RUS_COUNT',
                        CASE WHEN RULOGICALID like 'MCTR RUS%' THEN '6201' 
                            WHEN RULOGICALID like 'MCTR RRUS%' THEN '6601' 
                            ELSE 'RBS2000' END AS 'RBS_TYPE',
                        CASE WHEN RULOGICALID like 'MCTR RUS 01%' THEN 'RUS01' 
                            WHEN RULOGICALID like 'MCTR RUS 02%' THEN 'RUS02' 
                            WHEN RULOGICALID like 'MCTR RRUS 01%' THEN 'RRUS01' 
                            WHEN RULOGICALID like 'MCTR RRUS 02%' THEN 'RRUS02' 
                            WHEN RULOGICALID like 'MCTR RRUS 12%' THEN 'RRUS12' 
                            ELSE '2000' END AS 'RUS_TYPE'
                        FROM RXMFP
                        LEFT JOIN RXMOP USING(BSC,mo)
                        WHERE RULOGICALID LIKE 'MCTR%'
                        GROUP BY BSC,CELL
                        ORDER BY RUS_TYPE DESC
                    )t2 USING(BSC,CELL)
                    WHERE CELL NOT LIKE 'TEST%' AND CELL NOT LIKE 'YJ%'
                    ORDER BY BSC,CELL
                )TT1
                GROUP BY CSYSTYPE,UPGRATE_TYPE,TYPE) t";
        $rs  = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['totalCount'];
        // $sql   = "select * from ".$table.$filter.$limit;
        $sql = "SELECT
            CSYSTYPE AS '频段',
            UPGRATE_TYPE AS 'GSM升级到FDD',
            TYPE AS '说明',
            COUNT(DISTINCT SITE) AS '基站数',
            COUNT(CELL) AS '小区数'
            FROM
            (
                SELECT
                if(@sitename=LEFT(CELL,LENGTH(CELL)-1), null,@sitename) AS 'SITE',
                @sitename:=LEFT(CELL,LENGTH(CELL)-1),
                LEFT(CELL,LENGTH(CELL)-1) AS '',
                BSC,
                CELL,
                CSYSTYPE,
                DCHNO_COUNT,
                IFNULL(RBS_TYPE,'RBS2000') AS 'RBS_TYPE',
                RUS_TYPE,
                RUS_COUNT,
                CASE 
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND dchno_count<=8 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                ELSE '不能升级的射频单元型号' END AS 'UPGRATE_TYPE',
                CASE WHEN ISNULL(RBS_TYPE) THEN 'RBS2000'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6201+ rus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6201+ rus01*1（4<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6601+ rrus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6601+ rrus01*1（4<=载波数）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '宏站6201+ rus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '宏站6201+ rus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '分布式6601+ rrus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '分布式6601+ rrus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus12*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus12*1（7<=载波数<=8）'
                ELSE 'UNKNOWN' END AS 'TYPE'
                FROM RLDEP
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,
                    COUNT(DCHNO) AS 'DCHNO_COUNT'
                    FROM RLCFP
                    WHERE DCHNO<>''
                    GROUP BY BSC,CELL
                )t1 USING(BSC,CELL)
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,
                    COUNT(DISTINCT RUSERIALNO) AS 'RUS_COUNT',
                    CASE WHEN RULOGICALID like 'MCTR RUS%' THEN '6201' 
                        WHEN RULOGICALID like 'MCTR RRUS%' THEN '6601' 
                        ELSE 'RBS2000' END AS 'RBS_TYPE',
                    CASE WHEN RULOGICALID like 'MCTR RUS 01%' THEN 'RUS01' 
                        WHEN RULOGICALID like 'MCTR RUS 02%' THEN 'RUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 01%' THEN 'RRUS01' 
                        WHEN RULOGICALID like 'MCTR RRUS 02%' THEN 'RRUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 12%' THEN 'RRUS12' 
                        ELSE '2000' END AS 'RUS_TYPE'
                    FROM RXMFP
                    LEFT JOIN RXMOP USING(BSC,mo)
                    WHERE RULOGICALID LIKE 'MCTR%'
                    GROUP BY BSC,CELL
                    ORDER BY RUS_TYPE DESC
                )t2 USING(BSC,CELL)
                WHERE CELL NOT LIKE 'TEST%' AND CELL NOT LIKE 'YJ%'
                ORDER BY BSC,CELL
            )TT1
            GROUP BY CSYSTYPE,UPGRATE_TYPE,TYPE" . $limit;
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            // $qr = $this->substringParamData($qr);
            array_push($items, $qr);
        }

        $result['records'] = $items;
        return json_encode($result);
    }

    public function getStatisticDataHeader() {
        $dbname = Input::get('dataBase');

        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', $dbname);       
        $sql = "
            SELECT
            CGI AS '小区ECGI',
            agblk  AS 'GSM升级到FDD',
            bcchno  AS '说明(按各厂家设备与附件2 C列一致)',
            cgi AS '频段',
            bsic AS '载频数',
            CELL,
            BSC,
            LEFT(CELL,LENGTH(CELL)-1) AS '所属BTS',
            mfrms AS '基站类型',
            irc as '射频单元类型',
            bcchType AS 'RRU个数'
            FROM RLDEP
            LIMIT 1";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    public function getStatisticData() {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";
        $filter = '';

        $dbname = Input::get('dataBase');
        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', $dbname);

        $sql = "SELECT count(cgi) as 'totalCount' from RLDEP WHERE CELL NOT LIKE 'TEST%' AND CELL NOT LIKE 'YJ%'";
        $rs  = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['totalCount'];
        // $sql   = "select * from ".$table.$filter.$limit;
        $sql = "SELECT
                CGI AS '小区ECGI',
                CASE 
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND dchno_count<=8 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                ELSE '不能升级的射频单元型号' END AS 'GSM升级到FDD',
                CASE 
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6201+ rus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6201+ rus01*1（4<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6601+ rrus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6601+ rrus01*1（4<=载波数）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '宏站6201+ rus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '宏站6201+ rus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '分布式6601+ rrus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '分布式6601+ rrus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus12*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus12*1（7<=载波数<=8）'
                ELSE 'RBS2000' END AS '说明(按各厂家设备与附件2 C列一致)',
                CSYSTYPE AS '频段',
                DCHNO_COUNT AS '载频数',
                CELL,
                BSC,
                LEFT(CELL,LENGTH(CELL)-1) AS 'SITE',
                RUS_TYPE AS '所属BTS',
                RBS_TYPE AS '基站类型',
                RULOGICALID '射频单元类型',
                RUS_COUNT AS 'RRU个数'
                FROM RLDEP
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,
                    COUNT(DCHNO) AS 'DCHNO_COUNT'
                    FROM RLCFP
                    WHERE DCHNO<>''
                    GROUP BY BSC,CELL
                )t1 USING(BSC,CELL)
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,RULOGICALID,
                    COUNT(DISTINCT RUSERIALNO) AS 'RUS_COUNT',
                    CASE WHEN RULOGICALID like 'MCTR RUS%' THEN 'RBS6201' 
                        WHEN RULOGICALID like 'MCTR RRUS%' THEN 'RBS6601'
                        WHEN RULOGICALID like 'TRX DRU9P%' THEN 'RBS2116'
                        WHEN RULOGICALID like 'TRX DRU%' THEN 'RBS2216' 
                        WHEN RULOGICALID like 'TRX RRU_C%' THEN 'RBS2108' 
                        WHEN RULOGICALID like 'TRX RRU_M%' THEN 'RBS2308' 
                        WHEN RULOGICALID like 'TRX RRUM%' THEN 'RBS2308' 
                        WHEN RULOGICALID like 'TRX RRU_T%' THEN 'RBS2309' 
                        WHEN RULOGICALID like 'TRX RRU_N%' THEN 'RBS2111' 
                        WHEN RULOGICALID like 'TRX RRUN%' THEN 'RBS2111' 
                        WHEN RULOGICALID like 'TRX BSU%' THEN 'RBS2409' 
                        WHEN RULOGICALID like 'DMRU TRU%' THEN 'RBS2202' 
                        WHEN RULOGICALID like 'TRX STRU%' THEN 'RBS2202' 
                        WHEN RULOGICALID like 'TRX TRU%' THEN 'RBS2202' 
                        WHEN RULOGICALID like 'TRX DTRU%' THEN 'RBS2206' 
                        ELSE 'RBS2000' END AS 'RBS_TYPE',
                    CASE WHEN RULOGICALID like 'MCTR RUS 01%' THEN 'RUS01' 
                        WHEN RULOGICALID like 'MCTR RUS 02%' THEN 'RUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 01%' THEN 'RRUS01' 
                        WHEN RULOGICALID like 'MCTR RRUS 02%' THEN 'RRUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 12%' THEN 'RRUS12' 
                        ELSE '2000' END AS 'RUS_TYPE'
                    FROM RXMOP
                    LEFT JOIN RXMFP USING(BSC,MO)
                    WHERE CELL NOT IN('','ALL')
                    GROUP BY BSC,CELL
                )t2 USING(BSC,CELL)
                WHERE CELL NOT LIKE 'TEST%' AND CELL NOT LIKE 'YJ%'
                ORDER BY BSC,CELL" . $limit;
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            // $qr = $this->substringParamData($qr);
            array_push($items, $qr);
        }
        $result['records'] = $items;
        return json_encode($result);
    }

    public function getAllStatisticsData() {
        $column = "小区ECGI,GSM升级到FDD,说明(按各厂家设备与附件2 C列一致),频段,载频数,CELL,BSC,所属BTS,基站类型,射频单元类型,RRU个数";
        $dbname = Input::get('dataBase');
        $name = '现网设备情况'; 
        $fileName   = "files/".$dbname."_".$name."_".date('YmdHis').".csv";
        $sql = "SELECT
                CGI AS '小区ECGI',
                CASE 
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 THEN '可升级射频单元的型号-单扇区配置单通道'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND dchno_count<=8 THEN '可升级射频单元的型号-单扇区配置双通道（含单通道双拼和双通道模块）'
                ELSE '不能升级的射频单元型号' END AS 'GSM升级到FDD',
                CASE 
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6201+ rus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6201+ rus01*1（4<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT<=3 THEN '分布式6601+ rrus01*1（0<=载波数<=3）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT=1 AND DCHNO_COUNT>=4 THEN '分布式6601+ rrus01*1（4<=载波数）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT=1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*1（7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '宏站6201+ rus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '宏站6201+ rus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '宏站6201+ rus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6201' AND RUS_TYPE='RUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '宏站6201+ rus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus01*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS01' AND RUS_COUNT>1 AND DCHNO_COUNT>=7 THEN '分布式6601+ rrus01*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus02*2 （0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus02*2 （7<=载波数<=8）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS02' AND RUS_COUNT>1 AND DCHNO_COUNT>=9 THEN '分布式6601+ rrus02*2 （9<=载波数）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=6 THEN '分布式6601+ rrus12*1（0<=载波数<=6）'
                WHEN RBS_TYPE='6601' AND RUS_TYPE='RRUS12' AND DCHNO_COUNT<=8 THEN '分布式6601+ rrus12*1（7<=载波数<=8）'
                ELSE 'RBS2000' END AS '说明(按各厂家设备与附件2 C列一致)',
                CSYSTYPE AS '频段',
                DCHNO_COUNT AS '载频数',
                CELL,
                BSC,
                
                RUS_TYPE AS '所属BTS',
                RBS_TYPE AS '基站类型',
                RULOGICALID '射频单元类型',
                RUS_COUNT AS 'RRU个数'
                FROM RLDEP
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,
                    COUNT(DCHNO) AS 'DCHNO_COUNT'
                    FROM RLCFP
                    WHERE DCHNO<>''
                    GROUP BY BSC,CELL
                )t1 USING(BSC,CELL)
                LEFT JOIN 
                (
                    SELECT
                    BSC,CELL,RULOGICALID,
                    COUNT(DISTINCT RUSERIALNO) AS 'RUS_COUNT',
                    CASE WHEN RULOGICALID like 'MCTR RUS%' THEN 'RBS6201' 
                        WHEN RULOGICALID like 'MCTR RRUS%' THEN 'RBS6601'
                        WHEN RULOGICALID like 'TRX DRU9P%' THEN 'RBS2116'
                        WHEN RULOGICALID like 'TRX DRU%' THEN 'RBS2216' 
                        WHEN RULOGICALID like 'TRX RRU_C%' THEN 'RBS2108' 
                        WHEN RULOGICALID like 'TRX RRU_M%' THEN 'RBS2308' 
                        WHEN RULOGICALID like 'TRX RRUM%' THEN 'RBS2308' 
                        WHEN RULOGICALID like 'TRX RRU_T%' THEN 'RBS2309' 
                        WHEN RULOGICALID like 'TRX RRU_N%' THEN 'RBS2111' 
                        WHEN RULOGICALID like 'TRX RRUN%' THEN 'RBS2111' 
                        WHEN RULOGICALID like 'TRX BSU%' THEN 'RBS2409' 
                        WHEN RULOGICALID like 'DMRU TRU%' THEN 'RBS2202' 
                        WHEN RULOGICALID like 'TRX STRU%' THEN 'RBS2202' 
                        WHEN RULOGICALID like 'TRX TRU%' THEN 'RBS2202' 
                        WHEN RULOGICALID like 'TRX DTRU%' THEN 'RBS2206' 
                        ELSE 'RBS2000' END AS 'RBS_TYPE',
                    CASE WHEN RULOGICALID like 'MCTR RUS 01%' THEN 'RUS01' 
                        WHEN RULOGICALID like 'MCTR RUS 02%' THEN 'RUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 01%' THEN 'RRUS01' 
                        WHEN RULOGICALID like 'MCTR RRUS 02%' THEN 'RRUS02' 
                        WHEN RULOGICALID like 'MCTR RRUS 12%' THEN 'RRUS12' 
                        ELSE '2000' END AS 'RUS_TYPE'
                    FROM RXMOP
                    LEFT JOIN RXMFP USING(BSC,MO)
                    WHERE CELL NOT IN('','ALL')
                    GROUP BY BSC,CELL
                )t2 USING(BSC,CELL)
                WHERE CELL NOT LIKE 'TEST%' AND CELL NOT LIKE 'YJ%'
                ORDER BY BSC,CELL";
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', $dbname); 
        $res = $db->query($sql);
        if ($res) {
            $csvContent = mb_convert_encoding($column."\n", 'GBK');
            $fp         = fopen($fileName, "w");
            fwrite($fp, $csvContent);
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $newRow = array();
                foreach ($row as $key => $value) {
                    $newRow[$key] = mb_convert_encoding($value, 'GBK');
                }
                fputcsv($fp, $newRow);
            }
            fclose($fp);
            $result['fileName'] = $fileName;
            $result['result']   = true;
        } else {
            $result['result'] = false;
        }

        return $result;
    }

}