<?php
    function insertData_parameter($pdo, $host, $username, $password, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $tables, $table_all) {
        $pdo->query("UPDATE $tables SET `参数`=0 WHERE `参数` IS NULL");
        $pdo->query("UPDATE $tables SET `Polar-参数`=0 WHERE `Polar-参数` IS NULL");
        $pdo->query("UPDATE $tables SET `featureState`='none' WHERE featureState IS NULL;");
        $pdo->query("UPDATE $tables SET `licenseState`='none' WHERE licenseState IS NULL;");
        //判断是否用今天的数据
        $dbname = "kget".date("ymd");
        $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }
        // $yesDate = date("ymd",strtotime("-1 day"));
        // $dbname = 'kget' . $yesDate;
        $table = 'ParaCheckBaseline';
        $db = '';
        try {
            $db = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
        } catch (Exception $e) {
            return;
        }
        $sql = "select cellId,count(*) as num from  $table where cellId in $cellStr group by cellId;";

        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetchall(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $cell = $value['cellId'];
                $num = $value['num'];
                $pdo->query("UPDATE $tables SET `参数`=$num WHERE cell='$cell'");
            }
        }

        $sql = "select cellId,highTraffic from $table where cellId in $cellStr GROUP by cellId;";
        $res = $db->query($sql);
        $srUserFlag = 0;
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $meContext = $value['cellId'];
                $flag = $value['highTraffic'];
                $file = fopen($txt, "r");
                fgets($file);
                while (!feof($file)) {
                    $arr = explode("=", trim(fgets($file)));
                    $arrMeContext = $arr[1];
                    if ($arrMeContext == $value['cellId']) {
                        $cell = $arr[1];
                        if ($flag == "YES") {
                            $pdo->query("UPDATE $tables SET `Polar-参数`=100 WHERE `cell`='$cell';"); 
                            $srUserFlag = 1;
                        } else {
                            $sql = "SELECT count(*) as num from $table where cellId='$cell';";
                            $row_count = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                            if (count($row_count) == 0) {
                                $pdo->query("UPDATE $tables SET `Polar-参数`=0 WHERE cell='$cell';");
                            } else {
                                if ($row_count[0]['num'] == 0) {
                                    $pdo->query("UPDATE $tables SET `Polar-参数`=0 WHERE cell='$cell';");
                                } else {
                                    $pdo->query("UPDATE $tables SET `Polar-参数`=50 WHERE cell='$cell';");
                                }
                            }
                        }
                        
                    }
                }
                fclose($file);
            }
        }

        if ($srUserFlag == 0) {
            $sql = "SELECT cellId,count(*) as num from  EUtranCellTDD where cellId in $cellStr group by cellId;";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchall(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $cell = $value['cellId'];
                    $num = $value['num'];
                    $pdo->query("UPDATE $tables SET `参数`=$num WHERE cell='$cell'");
                }
            }
            $sql = "SELECT EUtranCellTDDId,noOfPucchSrUsers FROM EUtranCellTDD WHERE EUtranCellTDDId in $cellStr GROUP by EUtranCellTDDId;";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchAll(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $num = $value['noOfPucchSrUsers'];
                    $file = fopen($txt, "r");
                    fgets($file);
                    while (!feof($file)) {
                        $arr = explode("=", trim(fgets($file)));
                        $arrMeContext = $arr[1];
                        if ($arrMeContext == $value['EUtranCellTDDId']) {
                            $cell = $arr[1];
                            if ($num < 400) {
                                $srUserFlag = 1;
                                $pdo->query("UPDATE $tables SET `Polar-参数`=50 WHERE `cell`='$cell';"); 
                            }
                        }  
                    }
                }
            }
        }

        if ($srUserFlag == 0) {
            $sql = "SELECT cellId,count(*) as num from  EUtranCellFDD where cellId in $cellStr group by cellId;";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchall(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $cell = $value['cellId'];
                    $num = $value['num'];
                    $pdo->query("UPDATE $tables SET `参数`=$num WHERE cell='$cell'");
                }
            }
            $sql = "SELECT EUtranCellFDDId,noOfPucchSrUsers FROM EUtranCellFDD WHERE EUtranCellFDDId in $cellStr GROUP by EUtranCellFDDId;";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchAll(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $num = $value['noOfPucchSrUsers'];
                    $file = fopen($txt, "r");
                    fgets($file);
                    while (!feof($file)) {
                        $arr = explode("=", trim(fgets($file)));
                        $arrMeContext = $arr[1];
                        if ($arrMeContext == $value['EUtranCellFDDId']) {
                            $cell = $arr[1];
                            if ($num < 400) {
                                $srUserFlag = 1;
                                $pdo->query("UPDATE $tables SET `Polar-参数`=50 WHERE `cell`='$cell';"); 
                            }
                        }  
                    }
                }
            }
        }

        $table = "OptionalFeatureLicense";
        $sql = "SELECT meContext,serviceState,featureState,licenseState,OptionalFeatureLicenseId FROM $table WHERE meContext in $erbsStr AND (OptionalFeatureLicenseId = 'DynamicQosModification'OR OptionalFeatureLicenseId = 'InterFrequencyLteHandover' OR OptionalFeatureLicenseId = 'MultiErabsPerUser') AND serviceState='0 (INOPERABLE)' GROUP by meContext;";
        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $serviceState = $value['serviceState'];
                $featureState = $value['featureState'];
                $licenseState = $value['licenseState'];
                $OptionalFeatureLicenseId = $value['OptionalFeatureLicenseId'];
                $featureState = $featureState . "," . $OptionalFeatureLicenseId;
                $file = fopen($txt, "r");
                fgets($file);
                while (!feof($file)) {
                    $arr = explode("=", trim(fgets($file)));
                    $arrMeContext = $arr[2];
                    if ($arrMeContext == $value['meContext']) {
                        $cell = $arr[1];
                        if ($serviceState == '0 (INOPERABLE)') {
                        // print_r("UPDATE $tables SET `featureState`=$featureState WHERE cell='$cell';");
                            $pdo->query("UPDATE $tables SET `featureState`='$featureState' WHERE cell='$cell';");
                            $pdo->query("UPDATE $tables SET `licenseState`='$licenseState' WHERE cell='$cell';");
                        }
                    }
                }
                fclose($file);
            }
        }
    }

    function getCHCity($city, $pdo)
    {
        $sql    = "select cityChinese from mongs.databaseconn where connName='$city'";
        $row    = $pdo->query($sql)->fetchcolumn();
        $CHCity = $row;
        return $CHCity;
    }

    function getSubNets($city, $pdo)
    {
        $SQL           = "select if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD),subNetwork) subNetwork from mongs.databaseconn where cityChinese = '$city'";
        // $res           = DB::select($SQL);
        $res = $pdo->query($SQL)->fetchAll(PDO::FETCH_ASSOC);
        $subNetworkArr = array();
        $subNetworkStr = '';
        foreach ($res as $value) {

            $subNetworkStr .= '"'.str_replace(',', '","', $value['subNetwork']).'",';
        }
        $subNetworkStr = substr($subNetworkStr, 0, -1);
        // return $this->reCombine($subNetworkStr);
        return $subNetworkStr;
    }