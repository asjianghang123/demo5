<?php
    function insertData_parameter_badhandover($pdo, $host, $username, $password, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $cellArr, $tables, $table_all) {
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
        $db = '';
        try {
            $db = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
        } catch (Exception $e) {
            return;
        }

        foreach ($cellArr as $cell) {
            $value = 0;
            $canshu=0;
            $file = fopen($txt, "r");
            fgets($file);
            $city = '';
            $meContext = '';
            while (!feof($file)) {
                $arr = explode("=", trim(fgets($file)));
                $arrCell = $arr[1];
                if ($arrCell == $cell) {
                    $city = $arr[0];
                    $meContext = $arr[2];
                    break;
                }
            }
            fclose($file);
            // print_r($meContext.'-');
            // print_r($city."-".$cell."|");
            $cityCh = getCHCity($city, $pdo);
            $subNetwork = getSubNets($cityCh, $pdo);

            //MRE邻区排名前30的邻区有PCI一二阶冲突
            $filter = " where EutranCellTDD = '$cell' ";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
            $rs   = $db->query($sql1);
            if (!$rs) {
                continue;
            }
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $canshu=$row[0];
                $value = 100;
            } else {
                //二阶冲突
                $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
                $rs   = $db->query($sql2);
                if (!$rs) {
                    continue;
                }
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
            if ($value == 0) {
                //MME LIST定义不一致
                $filter = " where meContext = '$meContext'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempTermPointToMme_S1_MMEGI_dif ".$filter;
                $rs   = $db->query($sql);
                if (!$rs) {
                    continue;
                }
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
            if ($value == 0) {
                //X2接口定义不一致
                $filter = " where meContext = '$meContext'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                //X2 Used IP检查
                $sql1 = "select count(*) from TempTermPointToENB_ENBID_usedIpAddress".$filter;
                $rs   = $db->query($sql1);
                if (!$rs) {
                    continue;
                }
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                } else {
                    //X2-邻区eNbID检查
                    $sql2 = "select count(*) from TempTermPointToENB_IP".$filter;
                    $rs   = $db->query($sql2);
                    if (!$rs) {
                        continue;
                    }
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        $canshu=$row[0];
                        $value = 100;
                    }
                }
            }
            if ($value == 0) {
                //如果S1切换失败的占比高于50%且相关邻区ActivePLMNlist为空
                $filter = " where meContext = '$meContext'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempExternalEUtranCellTDDActivePlmnListCheck ".$filter;
                $rs   = $db->query($sql);
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
            if ($value == 0) {
                //切换准备失败次数的占比50%以上发生在，邻区外部定义不一致的邻区
                // $filter = " where meContext = '$meContext' and ExternalEUtranCellTDDId = '$ecgi_nr'";
                // if ($subNetwork != '') {
                //     $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                // }
                // $sql = "select count(*) from TempExternalNeigh4G ".$filter;
                // $rs   = $db->query($sql);
                // $row  = $rs->fetch(PDO::FETCH_NUM);
                // if ($row[0] > 0) {
                //     $value = 100;
                // }
            }
            if ($value == 0) {
                //如果S1切换失败的占比高于50%，相关TAC前三天定义过不同的MMEGI提示发生过TAC割接

            }

            if ($value == 0) {
                //4G测量频点数量多于5个
                $filter = " where EutranCellTDD = '$cell' and freqNum > 5";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempMeasuringFrequencyTooMuch ".$filter;
                $rs   = $db->query($sql);
                if (!$rs) {
                    continue;
                }
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }
            if ($value == 0) {
                //baseline中A类参数配置不一致的
                $templateId = 53;
                $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
                $sql = "select count(*) from ParaCheckBaseline".$filter;
                $rs   = $db->query($sql);
                if (!$rs) {
                    continue;
                }
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }
            $pdo->query("UPDATE $tables SET  `参数`=$canshu WHERE cell = '$cell'");
            $pdo->query("UPDATE $tables SET `Polar-参数`=$value WHERE cell = '$cell' ");
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