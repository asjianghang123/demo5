<?php
/**
* RealTimeInterferenceController.php
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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
* 实时干扰分析
* Class RealTimeInterferenceController
*
* @category Controllers
* @package  App\Http\Controllers
* @author   ericsson <genius@ericsson.com>
* @license  MIT License
* @link     https://laravel.com/docs/5.4/controllers
*/
class RealTimeInterferenceController extends Controller
{
    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'AutoKPI');
        $sql = "SELECT DISTINCT a.city,b.cityChinese FROM interfereCell_Cli as a LEFT JOIN mongs.databaseconn as b ON a.city = b.connName";
        $res = $db->query($sql);
        $items = array();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            array_push($items, array("label" => $r['cityChinese'], "value" => $r['city']));
        }
        echo json_encode($items);
    }//end getAllCity()

    /**
     * 获得日期列表
     *
     * @return void
     */
    public function getDateTime()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'AutoKPI');
        $sql = "SELECT DISTINCT date_id FROM interfereCell_Cli ORDER BY date_id DESC LIMIT 300";
        $res = $db->query($sql);
        $items = array();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $qr) {
            array_push($items, ["value" => $qr['date_id'], "label" => $qr['date_id']]);
        }
        echo json_encode($items);
    }//end getDateTime()

    /**
     * 获得实时数据
     *
     * @return void
     */
    public function getRealTimeData()
    {
        $dateTime = input::get("dateTime");
        $cityStr = input::get("city");
        $filter = " WHERE date_id = '$dateTime'";
        if ($cityStr) {
            $cityArr = explode(",", $cityStr);
            $city = implode("','", $cityArr);
            $filter = $filter . " AND baseTable.city in ('$city')";
        }
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'AutoKPI');
        $result = [];
        /*$sql = "SELECT
                    cellName,
                    siteName,
                    a.tac,
                    longitudeBD AS lng,
                    latitudeBD AS lat,
                    dir,
                    band,
                    tac_flag,
                    (
                        CASE
                        WHEN tac_flag = 0 THEN
                            - 120
                        ELSE
                            - 90
                        END
                    ) AS count
                FROM
                    mongs.siteLte AS a
                LEFT JOIN (
                    SELECT
                        tac,
                        (
                            CASE
                            WHEN sum(site_flag) / count(site_flag) > 0.7 THEN
                                1
                            ELSE
                                0
                            END
                        ) AS tac_flag
                    FROM
                        (
                            SELECT
                                siteName,
                                tac,
                                (
                                    CASE
                                    WHEN sum(cell_flag) > 0 THEN
                                        1
                                    ELSE
                                        0
                                    END
                                ) AS site_flag
                            FROM
                                (
                                    SELECT
                                        cell,
                                        siteName,
                                        tac,
                                        (
                                            CASE
                                            WHEN sf_result > 20 THEN
                                                1
                                            ELSE
                                                0
                                            END
                                        ) AS cell_flag
                                    FROM
                                        interfereCell_Cli AS baseTable
                                    LEFT JOIN mongs.siteLte AS siteLte ON baseTable.cell = siteLte.cellName
                                    $filter
                                ) AS cellTable
                            GROUP BY
                                siteName
                        ) AS tacTable
                    GROUP BY
                        tac
                ) AS b ON a.tac = b.tac
                WHERE
                    tac_flag IS NOT NULL";*/
        $sql = "SELECT
                    cellName,
                    siteName,
                    a.tac,
                    longitudeBD AS lng,
                    latitudeBD AS lat,
                    dir,
                    band,
                    tac_status,
                    (
                        CASE
                        WHEN tac_status = 'none' THEN
                            7
                        WHEN tac_status = 'low' THEN
                            12
                        WHEN tac_status = 'middle' THEN
                            17
                        WHEN tac_status = 'high' THEN
                            22
                        END
                    ) AS count
                FROM
                    mongs.siteLte AS a
                LEFT JOIN (
                    SELECT
                        tac,
                        (
                            CASE
                            WHEN s20 / total > 0.7 THEN
                                'high'
                            WHEN s15 / total > 0.7 THEN
                                'middle'
                            WHEN s10 / total > 0.7 THEN
                                'low'
                            ELSE
                                'none'
                            END
                        ) AS tac_status
                    FROM
                        (
                            SELECT
                                tac,
                                COUNT(1) AS 'total',
                                sum(
                                    CASE
                                    WHEN site_result < 10 THEN
                                        1
                                    ELSE
                                        0
                                    END
                                ) AS 's0',
                                sum(
                                    CASE
                                    WHEN site_result >= 10 THEN
                                        1
                                    ELSE
                                        0
                                    END
                                ) AS 's10',
                                sum(
                                    CASE
                                    WHEN site_result >= 15 THEN
                                        1
                                    ELSE
                                        0
                                    END
                                ) AS 's15',
                                sum(
                                    CASE
                                    WHEN site_result >= 20 THEN
                                        1
                                    ELSE
                                        0
                                    END
                                ) AS 's20'
                            FROM
                                (
                                    SELECT
                                        siteName,
                                        tac,
                                        MAX(sf_result) AS site_result
                                    FROM
                                        (
                                            SELECT
                                                cell,
                                                siteName,
                                                tac,
                                                sf_result
                                            FROM
                                                interfereCell_Cli AS baseTable
                                            LEFT JOIN mongs.siteLte AS siteLte ON baseTable.cell = siteLte.cellName
                                            $filter
                                        ) AS cellTable
                                    GROUP BY
                                        siteName
                                ) AS siteTable
                            GROUP BY
                                tac
                        ) AS tacTable
                ) AS b ON a.tac = b.tac
                WHERE
                    tac_status IS NOT NULL";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }
        $result['mapData'] = $items;

        $sql = "SELECT
                    *, (
                        CASE
                        WHEN (
                            high_interfere_stat = 'yes'
                            AND connection_stat = 'unavailable'
                        ) THEN
                            '长时间持续高干扰&无法连接ENB'
                        WHEN (
                            high_interfere_stat = 'yes'
                            AND connection_stat = 'available'
                        ) THEN
                            '长时间持续高干扰'
                        WHEN (
                            high_interfere_stat = 'no'
                            AND connection_stat = 'unavailable'
                        ) THEN
                            '无法连接ENB'
                        END
                    ) AS cell_state
                FROM
                    interfereCell_Cli_info as baseTable
                    $filter 
                AND (
                    high_interfere_stat = 'yes'
                    OR connection_stat = 'unavailable'
                )";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items1 = array();
        foreach ($row as $qr) {
            array_push($items1, $qr);
        }
        $result['tableData'] = $items1;

        /*$sql = "SELECT
					tac,
					tac_flag
				FROM
					(
						SELECT
							tac,
							(
								CASE
								WHEN sum(site_flag) / count(site_flag) > 0.7 THEN
									1
								ELSE
									0
								END
							) AS tac_flag
						FROM
							(
								SELECT
									siteName,
									tac,
									(
										CASE
										WHEN sum(cell_flag) > 0 THEN
											1
										ELSE
											0
										END
									) AS site_flag
								FROM
									(
										SELECT
											cell,
											siteName,
											tac,
											(
												CASE
												WHEN sf_result > 20 THEN
													1
												ELSE
													0
												END
											) AS cell_flag
										FROM
											interfereCell_Cli AS baseTable
										LEFT JOIN mongs.siteLte AS siteLte ON baseTable.cell = siteLte.cellName
										$filter
									) AS cellTable
								GROUP BY
									siteName
							) AS tacTable
						GROUP BY
							tac
					) AS a
				WHERE
					tac_flag = 1
                ORDER BY
                    tac";*/
        $sql = "SELECT
                    tac,
                    tac_status
                FROM
                    (
                        SELECT
                            tac,
                            (
                                CASE
                                WHEN s20 / total > 0.7 THEN
                                    '>20'
                                WHEN s15 / total > 0.7 THEN
                                    '15-20'
                                WHEN s10 / total > 0.7 THEN
                                    '10-15'
                                ELSE
                                    '<10'
                                END
                            ) AS tac_status
                        FROM
                            (
                                SELECT
                                    tac,
                                    COUNT(1) AS 'total',
                                    sum(
                                        CASE
                                        WHEN site_result < 10 THEN
                                            1
                                        ELSE
                                            0
                                        END
                                    ) AS 's0',
                                    sum(
                                        CASE
                                        WHEN site_result >= 10 THEN
                                            1
                                        ELSE
                                            0
                                        END
                                    ) AS 's10',
                                    sum(
                                        CASE
                                        WHEN site_result >= 15 THEN
                                            1
                                        ELSE
                                            0
                                        END
                                    ) AS 's15',
                                    sum(
                                        CASE
                                        WHEN site_result >= 20 THEN
                                            1
                                        ELSE
                                            0
                                        END
                                    ) AS 's20'
                                FROM
                                    (
                                        SELECT
                                            siteName,
                                            tac,
                                            MAX(sf_result) AS site_result
                                        FROM
                                            (
                                                SELECT
                                                    cell,
                                                    siteName,
                                                    tac,
                                                    sf_result
                                                FROM
                                                    interfereCell_Cli AS baseTable
                                                LEFT JOIN mongs.siteLte AS siteLte ON baseTable.cell = siteLte.cellName
                                                $filter
                                            ) AS cellTable
                                        GROUP BY
                                            siteName
                                    ) AS siteTable
                                GROUP BY
                                    tac
                            ) AS tacTable
                    ) AS a
                WHERE
                    tac_status != '<10'
                ORDER BY
                    tac";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items2 = array();
        foreach ($row as $qr) {
            array_push($items2, $qr);
        }
        $result['tac'] = $items2;
        echo json_encode($result);
    }//end getRealTimeData()
}