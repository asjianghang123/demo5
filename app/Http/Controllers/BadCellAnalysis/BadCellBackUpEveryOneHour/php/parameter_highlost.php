<?php
    function insertData_parameter_highlost($pdo, $host, $username, $password, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $cellArr, $tables, $table_all) {
        $pdo->query("UPDATE $tables SET `参数`=0 WHERE `参数` IS NULL");
        $pdo->query("UPDATE $tables SET `Polar-参数`=0 WHERE `Polar-参数` IS NULL");
        $pdo->query("UPDATE $tables SET `featureState`='none' WHERE featureState IS NULL;");
        $pdo->query("UPDATE $tables SET `licenseState`='none' WHERE licenseState IS NULL;");
        // $yesDate = date("ymd",strtotime("-1 day"));
        // $dbname = 'kget' . $yesDate;
        //判断是否用今天的数据
        $dbname = "kget".date("ymd");
        $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }
        // $table = 'ParaCheckBaseline';
        $db = '';
        try {
            $db = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
        } catch (Exception $e) {
            return;
        }

        // $date = new DateTime();    
        // $date->sub(new DateInterval('P1D'));
        // $yesDate = $date->format('ymd');

        foreach ($cellArr as $cell) {
            $value = 0;
            $canshu=0;
            $file = fopen($txt, "r");
            fgets($file);
            $city = '';
            while (!feof($file)) {
                $arr = explode("=", trim(fgets($file)));
                $arrCell = $arr[1];
                if ($arrCell == $cell) {
                    $city = $arr[0];
                    break;
                }
            }
            $cityCh = getCHCity($city, $pdo);
            $subNetwork = getSubNets($cityCh, $pdo);
            
            if ($value == 0) {
                //掉线差小区参数关联cellbarred小区参数为0的，即EUtranCellTDD/EUtranCellFDD这个MO下面的参数cellBarred设置为0的，则进行输出报错，评分为100分
                    //EUtranCellTDD
                $filter = " WHERE cellbarred='0 (BARRED)' AND EUtranCellTDDId = '$cell'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "SELECT COUNT(*) AS num FROM EUtranCellTDD".$filter;
                $rs = $db->query($sql);
                if ($rs) {
                    $row  = $rs->fetch(PDO::FETCH_ASSOC);
                    if ($row[0]['num'] > 0) {
                        $canshu=$row[0]['num'];
                        $value = 100;
                    } else {
                        //EUtranCellFDD
                        $filter = " WHERE cellbarred='0 (BARRED)' AND EUtranCellFDDId = '$cell'";
                        if ($subNetwork != '') {
                            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                        }
                        $sql = "SELECT COUNT(*) AS num FROM EUtranCellFDD".$filter;
                        $rs = $db->query($sql);
                        if ($rs) {
                            $row  = $rs->fetch(PDO::FETCH_ASSOC);
                            if ($row[0]['num'] > 0) {
                                $canshu=$row[0]['num'];
                                $value = 100;
                            } 
                        }
                    }
                }

                //MRE邻区排名前30的邻区有PCI一二阶冲突
                $filter = " where EutranCellTDD = '$cell'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                //一阶冲突
                $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
                $rs   = $db->query($sql1);
                if ($rs) {       
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        $canshu=$row[0];
                        $value = 100;
                    } else {
                        //二阶冲突
                        $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
                        $rs   = $db->query($sql2);
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        if ($row[0] > 0) {
                            $canshu=$row[0];
                            $value = 100;
                        }
                    }
                }
                //没有定义本小区freqrel
                if ($value == 0) {
                    $filter = " where EutranCellTDDId = '$cell'";
                    if ($subNetwork != '') {
                        $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                    }
                    $sql = "select count(*) from TempMissEqualFrequency ".$filter;
                    $rs   = $db->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        if ($row[0] > 0) {
                            $canshu=$row[0];
                            $value = 100;
                        }
                    } 
                }
                //未定邻区
                if ($value == 0) {
                    $filter = " where EutranCellTDD = '$cell' and remark3 = 'NoneCellRelation'";
                    if ($subNetwork != '') {
                        $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                    }
                    $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
                    $rs   = $db->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        if ($row[0] > 0) {
                            $canshu=$row[0];
                            $value = 100;
                        }
                    }
                }
                //没有定义同站同频邻区
                if ($value == 0) {
                    $filter = " where EutranCellTDD = '$cell' and remark1 = 'co-SiteNeighborRelationMiss'";
                    if ($subNetwork != '') {
                        $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                    }
                    $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
                    $rs   = $db->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        if ($row[0] > 0) {
                            $canshu=$row[0];
                            $value = 50;
                        }
                    } 
                }
                //邻区过少
                if ($value == 0) {
                    $filter = " where EutranCellTDD = '$cell'";
                    if ($subNetwork != '') {
                        $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                    }
                    $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
                    $rs   = $db->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        if ($row[0] > 0) {
                            $canshu=$row[0];
                            $value = 50;
                        }
                    }      
                }
                //baseline中A类参数配置不一致的
                if ($value == 0) {
                    $sql = "select siteName from mongs.siteLte where cellName = '$cell'";
                    $rs   = $pdo->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        $meContext = $row[0];

                        $templateId = 53;
                        $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
                        $sql = "select count(*) from ParaCheckBaseline".$filter;
                        $rs   = $db->query($sql);
                        if ($rs) {
                            $row  = $rs->fetch(PDO::FETCH_NUM);
                            if ($row[0] > 0) {
                                $canshu=$row[0];
                                $value = 50;
                            }
                        }       
                    } 
                }
                $pdo->query("UPDATE $tables SET `参数`=$canshu WHERE cell = '$cell' ");
                $pdo->query("UPDATE $tables SET `Polar-参数`=$value WHERE cell = '$cell'");
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