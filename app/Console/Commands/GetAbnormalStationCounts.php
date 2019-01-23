<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use Illuminate\Support\Facades\DB;
use Config;
use PDO;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Models\WK_APP\Report_LatLonDirCheck_Combined;
use App\Models\WK_APP\Report_60degreeSectorDirCheck;
use App\Models\WK_APP\Report_100mCoEarfcnSiteCheck;
use App\Models\Mongs\AbnormalStationCounts;
use App\Models\Mongs\NewSiteLte;

class GetAbnormalStationCounts extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetAbnormalStationCounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GetAbnormalStationCounts';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $citys = ['changzhou','nantong','suzhou','wuxi','zhenjiang'];
        $dateId = date("Y-m-d");
        foreach ($citys as $city) {
            $result = $this->getAbnormalStationCounts($city);
            $flag = AbnormalStationCounts::where('dateId', $dateId)->where("city", $city)->exists();
            if ($flag) {
                AbnormalStationCounts::where('dateId', $dateId)
                                        ->where("city", $city)
                                        ->update(array(
                                            'new_station_count'=>$result['new_station_count'],
                                            'data_loss_count'=>$result['data_loss_count'],
                                            'repaeting_site_count'=>$result['repaeting_site_count'],
                                            'illegal_format_count'=>$result['illegal_format_count'],
                                            'cross_station_count'=>$result['cross_station_count'],
                                            'same_station_count'=>$result['same_station_count'],
                                            'azimuth_check_count'=>$result['azimuth_check_count'],
                                            'lon_lat_check_count'=>$result['lon_lat_check_count']
                                        ));
            } else {
                $AbnormalStationCounts = new AbnormalStationCounts;
                $AbnormalStationCounts->dateId = $dateId;
                $AbnormalStationCounts->city = $city;
                $AbnormalStationCounts->new_station_count = $result['new_station_count'];
                $AbnormalStationCounts->data_loss_count = $result['data_loss_count'];
                $AbnormalStationCounts->repaeting_site_count = $result['repaeting_site_count'];
                $AbnormalStationCounts->illegal_format_count = $result['illegal_format_count'];
                $AbnormalStationCounts->cross_station_count = $result['cross_station_count'];
                $AbnormalStationCounts->same_station_count = $result['same_station_count'];
                $AbnormalStationCounts->azimuth_check_count = $result['azimuth_check_count'];
                $AbnormalStationCounts->lon_lat_check_count = $result['lon_lat_check_count'];
                $AbnormalStationCounts->save();
            }

        }
    }

    /**
     * 异常基站站点数据数量
     *
     * @return mixed
     */
    public function getAbnormalStationCounts($city)
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $result = [];
        // new_station_count
        $result['new_station_count'] = NewSiteLte::where('city', $city)->count();
        

        // data_loss_count
        $sql = "SELECT count(*) as num FROM siteLte WHERE (ecgi IS NULL OR length(ecgi) = 0
                                                        OR ECI IS NULL OR length(ECI) = 0
                                                        OR cellName IS NULL OR length(cellName) = 0
                                                        OR cellNameChinese IS NULL OR length(cellNameChinese) = 0
                                                        OR siteName IS NULL OR length(siteName) = 0
                                                        OR siteNameChinese IS NULL OR length(siteNameChinese) = 0
                                                        OR duplexMode IS NULL OR length(duplexMode) = 0
                                                        OR rsi IS NULL OR length(rsi) = 0
                                                        OR tac IS NULL OR length(tac) = 0
                                                        OR longitude IS NULL OR length(longitude) = 0
                                                        OR latitude IS NULL OR length(latitude) = 0
                                                        OR dir IS NULL OR length(dir) = 0
                                                        OR pci IS NULL OR length(pci) = 0
                                                        OR earfcn IS NULL OR length(earfcn) = 0
                                                        OR cellType IS NULL OR length(cellType) = 0
                                                        OR tiltM IS NULL OR length(tiltM) = 0
                                                        OR tiltE IS NULL OR length(tiltE) = 0
                                                        OR antHeight IS NULL OR length(antHeight) = 0
                                                        OR address IS NULL OR length(address) = 0
                                                        OR band IS NULL OR length(band) = 0
                                                        OR city IS NULL OR length(city) = 0)";

        if ($city != 'city') {
            $sql .= " and city = '$city'";
        }
        $row = $db->query($sql)->fetch();
        $result['data_loss_count'] = $row['num'];

        // repaeting_site_count
        $sql = "select count(*) as num from (select count(*) as count,ecgi,siteName,address from siteLte where city = '$city' group by ecgi,siteName,address)as a where count > 1";
        $row = $db->query($sql)->fetch();
        $result['repaeting_site_count'] = $row['num'];

        // illegal_format_count
        $sql = "select count(*) as num from siteLte where (ecgi not REGEXP '^([0-9]+)-([0-9]+)-([0-9]+)$'
                                                        or duplexMode not in ('TDD','FDD','NB')
                                                        or (duplexMode = 'TDD' and band not in ('D','E','F'))
                                                        or (duplexMode in ('FDD','NB') and band not in ('band3','band8'))
                                                        or cellType not in ('Outdoor-macro-sector','Outdoor-macro-omni','Outdoor-micro-sector','Outdoor-micro-omni','Indoor-IBS'))";
        if ($city != 'city') {
            $sql .= " and city = '$city'";
        }
        $row = $db->query($sql)->fetch();
        $result['illegal_format_count'] = $row['num'];

        // 工参数据分析的4个指标数量
        // 首先要获取日期
        $date = Report_LatLonDirCheck_Combined::orderBy('date_id','desc')->first(['date_id'])->toArray()['date_id'];

        // cross_station_count
        $result['cross_station_count'] = $this->getTableDataCount(1, $date, $city);
        // same_station_count
        $result['same_station_count'] = $this->getTableDataCount(2, $date, $city);
        // azimuth_check_count
        $result['azimuth_check_count'] = $this->getTableDataCount(3, $date, $city);
        // lon_lat_check_count
        $result['lon_lat_check_count'] = $this->getTableDataCount(4, $date, $city);

        return $result;
    }

    /**
     * 工参数据查询数量
     *
     * @return mixed
     */
    public function getTableDataCount($action, $date, $city)
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $table = "";
        switch ($action) {
            case '1':
                $conn = new Report_100mCoEarfcnSiteCheck;
                $table = "Report_100mCoEarfcnSiteCheck";
                break;
            case '2':
                $conn = new Report_60degreeSectorDirCheck;
                $table = "Report_60degreeSectorDirCheck";
                break;
            case '3':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dir','>45degree');
                $table = "Report_LatLonDirCheck_Combined";
                break;
            case '4':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dist','>1km');
                $table = "Report_LatLonDirCheck_Combined";
                break;
        }

        $conn = $conn->where('s.city',$city)
                        ->where('date_id', $date)
                        ->leftJoin('mongs.siteLte as s', 's.ecgi', '=', $table.'.ECGI');
        $columns = ['*'];
        if ($action == 1) {
            $conn = $conn->leftJoin('mongs.siteLte as t', 't.ecgi', '=', $table.'.n_ECGI');
        }
        $rows = $conn->paginate($limit)
                    ->toArray();
        return $rows['total'];
    }
}