<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/4
 * Time: 17:08
 */

namespace App\Http\Controllers\QueryAnalysis;


class SubnetWorkService
{ 
  // protected $oss;
    public function getSubnetWork($oss) {
      $SN = "";
        switch ($oss) {
          case 'wuxiENM':
            $SN = "substring(substring(SN, 0, charindex(',',SN)-1), 12)";
            break;
          case "wuxi1":
            $SN = "substring(SN, 12, charindex(',', SN)-12)";
            break;
          case "wuxi":
            $SN = "substring(SN, 12, charindex(',', SN)-12)";
            break;
          case "suzhou3":
            $SN = "substring(SN, 12, charindex(',', SN)-12)";
            break;
            case "zhenjiang":
              $SN = "substring(substring(SN, 0, charindex(',',SN)-1), 12)";
            break;
            case "zhenjiang1":
              $SN = "substring(substring(SN, charindex(',', SN)+12), 0, charindex(',', substring(SN, charindex(',', SN)+12))-1)";
              break;
            case "changzhou3":
              $SN = "substring(SN, charindex('=', SN)+1, charindex(',', SN)-charindex('=', SN)-1)";
              break;
          default:
            $SN = "substring(SN,charindex('=',substring(SN,32,25))+32,charindex(',',substring(SN,32,25))-charindex('=',substring(SN,32,25))-1)";
            break;
        }
        return $SN;
    }

    public function getSN($format, $oss) {
      $SN = "";
      switch ($format) {
        case 'NBIOT':
          switch ($oss) {
            case 'wuxiENM':
              $SN = "SN  as site";
              break;
            case "suzhou3":
              $SN = "SN  as site";
              break;
            default:
              $SN = "substring(substring(SN,charindex (',', substring(SN, 32, 25)) + 32),11,25) as site";
              break;
          }
          break;
        default:
          switch ($oss) {
            case 'wuxiENM':
              $SN = "SN  as site";
              break;
                case "zhenjiang1":
                    $SN = "substring(substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))), charindex('=', substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))))+1) as site";
                  break;
                case "zhenjiang":
                    $SN = "substring(substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))), charindex('=', substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))))+1) as site";
                    break;
            case "suzhou3":
              $SN = "substring(substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))), charindex('=', substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))))+1) as site";
              break;
            case "wuxi1":
                    $SN = "substring(substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))), charindex('=', substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))))+1) as site";
              break;
                case "wuxi":
                    $SN = "substring(substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))), charindex('=', substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))))+1) as site";
                    break;
                case "changzhou3":
                    $SN = "substring(substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))), charindex('=', substring(substring(SN, charindex (',', SN)+1), charindex(',', substring(SN, charindex (',', SN)+1))+1, char_length(substring(SN, charindex (',', SN)+1))))+1) as site";
                    break;
            default:
            $SN = "substring(substring(SN,charindex (',', substring(SN, 32, 25)) + 32),11,25) as site";
            break;
          }
          break;
      }
      return $SN;
    }

    public function is_ReplaceDc($sql, $city) {
      switch ($city) {
        case "changzhou3":
          $sql = str_replace("dc.", "", $sql);
          break;
        default:
          $sql = $sql;
          break;
      }
      return $sql;
    }

    // public function __construct($oss)
    // {  
    //  $this->oss = $oss;
    // }
}