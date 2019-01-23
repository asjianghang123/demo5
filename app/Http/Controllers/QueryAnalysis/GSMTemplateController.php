<?php

/**
 * GSMTemplateController.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\Users;
use App\Models\Mongs\Template_2G;
use App\Models\Mongs\Kpiformula2G;

/**
 * GSM模板管理
 * Class GSMTemplateController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class GSMTemplateController extends GetTreeData
{


    /**
     * 获得模板列表
     *
     * @return void
     */
    public function getTreeData()
    {
        $login_user = Auth::user()->user;
        // if ($login_user == "admin") {
        //     $users      = Template_2G::distinct('user')->get(['user']);
        // } else {
        //     $users      = Template_2G::whereIn("user",[$login_user,"admin"])->distinct('user')->get(['user']);
        // }
        $users      = Template_2G::distinct('user')->get(['user']);
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = Template_2G::where('user', $userStr)->orderBy('templateName', 'asc')->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
            }

            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }

        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = Users::where('user', $user)->first(); 
                if ($nameCNSql) {
                    $itArr[$i]['text'] = $nameCNSql->name;
                }
            }
        }

        return response()->json($itArr);

    }//end getTreeData()


    /**
     * 检索指定模板
     *
     * @return mixed
     */
    public function searchGSMTreeData()
    {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";
        $login_user = Auth::user()->user;
        // if ($login_user == "admin") {
        //     $users      = Template_2G::distinct('user')->get(['user']);
        // } else {
        //     $users      = Template_2G::whereIn("user",[$login_user,"admin"])->distinct('user')->get(['user']);
        // }
        $users      = Template_2G::distinct('user')->get(['user']);
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $conn = Template_2G::where('user', $userStr)->where('templateName', 'like', $inputData);
            if ($conn->exists()) {
                $templateNames = $conn->orderBy('templateName', 'asc')->get()->toArray();
                foreach ($templateNames as $templateName) {
                    $temp['text'] = $templateName['templateName'];
                    $temp['id']   = $templateName['id'];
                    array_push($arrUser, $temp);
                }

                if ($userStr == "admin") {
                    $items["text"] = "通用模板";
                } else if ($userStr == "system") {
                    $items["text"] = "系统模板";
                } else {
                    $nameCNSql         = Users::where('user', $userStr)->first(); 
                    if ($nameCNSql) {
                        $items['text'] = $nameCNSql->name;
                    }
                }

                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        }//end foreach

        return response()->json($itArr);

    }//end searchGSMTreeData()


    /**
     * 获得指标列表
     *
     * @return void
     */
    public function getElementTree()
    {
        $templateId = input::get('templateId');
        $elementId = Template_2G::where('id', $templateId)->first();
        echo json_encode($elementId);

    }//end getElementTree()


    /**
     * 获得指标显示名
     *
     * @return void
     */
    public function getKpiNamebyId()
    {
        $idarr = explode(',', input::get("id"));
        $items = array();
        foreach ($idarr as $id) {
            $row = Kpiformula2G::where('id', $id)->first();
            if ($row) {
                $data['text'] = $row['kpiName'];
                $data['id']   = $row['id'];
                $data['user'] = $row['user'];
                array_push($items, $data);
            }
        }
        echo json_encode($items);

    }//end getKpiNamebyId()


    /**
     * 获得模板信息列表
     *
     * @return void
     */
    public function getTreeTemplate()
    {
        $login_user = Auth::user()->user;
        $rsUsersArr = Kpiformula2G::distinct('user')->get(['user'])->toArray();
        $idNum   = 1;
        $items   = array();
        $result = array();
        foreach ($rsUsersArr as $row) {
            //0609
            /*if ($login_user != 'admin' && $row['user'] != "admin" && $row['user'] != $login_user) {
                continue;
            }*/
            $result['id'] = $idNum++;
            if ($row['user'] == "admin") {
                $result['kpiName'] = "通用模板";
            } else {
                $user = Users::where('user', $row['user'])->first();
                if ($user) {
                    $result['kpiName'] = $user->name;
                } else {
                    $result['kpiName'] = $row['user'];
                }
            }

            $result['state'] = 'closed';
            $children        = array();
            $user            = $row['user'];
            $resArr = Kpiformula2G::where('user', $user)->orderBy('kpiName', 'asc')->get()->toArray();
            foreach ($resArr as $rs) {
                $kpiName      = $rs['kpiName'];
                $kpiFormula   = $rs['kpiFormula'];
                $kpiPrecision = $rs['kpiPrecision'];
                $kpiId        = $rs['id'];
                array_push($children, array("id" => $kpiId, "kpiName" => $kpiName, "kpiFormula" => $kpiFormula, "kpiPrecision" => $kpiPrecision));
            }
            $result['children'] = $children;
            array_push($items, $result);
        }//end foreach

        echo json_encode($items);

    }//end getTreeTemplate()


    /**
     * 更新指标公式
     *
     * @return void
     */
    public function updateFormula()
    {
        $id        = input::get("formulaId");
        $name      = input::get("name");
        $precision = input::get("precision");
        $formula   = input::get("formula");
        $user = Auth::user()->user;
        if ($id) {
            if ($user == "admin") {
                $res = Kpiformula2G::where('id', $id)->update([
                        'kpiName'=>$name,
                        'kpiFormula'=>$formula,
                        'kpiPrecision'=>$precision
                    ]);
            } else {
                $res = Kpiformula2G::where('id', $id)->where('user', $user)->update([
                        'kpiName'=>$name,
                        'kpiFormula'=>$formula,
                        'kpiPrecision'=>$precision
                    ]);
            }
        } else {
            $newKpiformula2G = new Kpiformula2G;
            $newKpiformula2G->kpiName = $name;
            $newKpiformula2G->kpiFormula = $formula;
            $newKpiformula2G->kpiPrecision = $precision;
            $newKpiformula2G->user = $user;
            $res = $newKpiformula2G->save();
        }

        if ($res) {
            echo true;
        } else {
            echo false;
        }

    }//end updateFormula()


    /**
     * 删除指标公式
     *
     * @return void
     */
    public function deleteFormula()
    {
        $id   = input::get("id");
        $user = Auth::user()->user;
        if ($user == "admin") {
            $res = Kpiformula2G::destroy($id);
        } else {
            $res = Kpiformula2G::where('id', $id)->where('user', $user)->delete();
        }
        if (!$res) {
            echo false;
            return;
        }

        // 删除后更新template表中相关内容
        $resTem = Template_2G::get()->toArray();
        $row      = [];
        $i        = 0;

        foreach ($resTem as $rowTem) {
            $row[$i++] = $rowTem['elementId'];
        }

        $rowTem   = [];
        $rowTem_1 = [];
        $k        = 0;
        for ($i = 0; $i < count($row); $i++) {
            $rowTem[$i] = explode(',', $row[$i]);
            for ($j = 0; $j < count($rowTem[$i]); $j++) {
                if ($rowTem[$i][$j] == $id) {
                    $rowTem_1[$k] = implode(',', $rowTem[$i]);
                    $k++;
                }
            }
        }

        $finalRow_0 = [];
        $finalRow_1 = [];
        $finalRow   = [];
        for ($i = 0; $i < count($rowTem_1); $i++) {
            $m = 0;
            $finalRow_0[$i] = explode(',', $rowTem_1[$i]);
            $finalRow_1[$i] = array();
            for ($j = 0; $j < count($finalRow_0[$i]); $j++) {
                if ($finalRow_0[$i][$j] != $id) {
                    $finalRow_1[$i][$m] = $finalRow_0[$i][$j];
                    $m++;
                }
            }

            $finalRow[$i] = implode(',', $finalRow_1[$i]);
        }

        $sql_update = [];
        for ($i = 0; $i < count($finalRow); $i++) {
            $old            = $rowTem_1[$i];
            $new            = $finalRow[$i];
            Template_2G::where('elementId', $old)->update(['elementId'=>$new]);
        }

        echo true;

    }//end deleteFormula()


    /**
     * 检索指标
     *
     * @return void
     */
    public function searchTreeTemplate()
    {
        $formula = input::get("formula");
        $formula = "%".$formula."%";
        $rsUsersArr = Kpiformula2G::distinct('user')->get(['user'])->toArray();
        // 筛选所有user
        $idNum = 1;
        $items = array();
        $result = array();
        foreach ($rsUsersArr as $row) {
            $result['id'] = $idNum++;
            if ($row['user'] == "admin") {
                $result['kpiName'] = "通用模板";
            } else {
                $user = Users::where('user', $row['user'])->first();
                if ($user) {
                    $result['kpiName'] = $user->name;
                } else {
                    $result['kpiName'] = $row['user'];
                }
            }

            $result['state'] = 'closed';
            $children        = array();
            $user            = $row['user'];
            $res = Kpiformula2G::where('user', $user)
                ->where(function($query) use($formula) {
                    $query->where('kpiFormula', 'like', $formula)
                        ->orWhere('kpiName', 'like', $formula);
                });
            if ($res->exists()) {
                $resArr =    $res->orderBy('kpiName', 'asc')->get()->toArray();
                foreach ($resArr as $rs) {
                    $kpiName      = $rs['kpiName'];
                    $kpiFormula   = $rs['kpiFormula'];
                    $kpiPrecision = $rs['kpiPrecision'];
                    $kpiId        = $rs['id'];
                    array_push($children, array("id" => $kpiId, "kpiName" => $kpiName, "kpiFormula" => $kpiFormula, "kpiPrecision" => $kpiPrecision));
                }
                $result['children'] = $children;
                array_push($items, $result);
            }
        }//end foreach
        echo json_encode($items);

    }//end searchTreeTemplate()


    /**
     * 更新指标
     *
     * @return void
     */
    public function updateElement()
    {
        $id   = input::get('id');
        $ids  = input::get('ids');
        $user = Auth::user()->user;
        if ($user == "admin") {
            $exeres = Template_2G::where('id', $id)->update(['elementId'=>$ids]);
        } else {
            $exeres = Template_2G::where('id', $id)->where('user', $user)->update(['elementId'=>$ids]);
        }
        if ($exeres) {
            echo true;
        } else {
            echo false;
        }

    }//end updateElement()


    /**
     * 新建模板
     *
     * @return void
     */
    public function addMode()
    {
        $templateName = input::get('modeName');
        $description  = input::get('modeDescription');
        $user = Auth::user()->user;
        $result = Template_2G::where('templateName', $templateName)->where('user', $user)->count();
        if ($result != 0) {
            echo "名称已有";
            return;
        }
        $newTemplate_2G = new Template_2G;
        $newTemplate_2G->templateName = $templateName;
        $newTemplate_2G->description = $description;
        $newTemplate_2G->user = $user;
        $res =$newTemplate_2G->save();
        if ($res) {
            echo true;
        } else {
            echo false;
        }

    }//end addMode()


    /**
     * 删除模板
     *
     * @return void
     */
    public function deleteMode()
    {
        $id = input::get('id');
        $user = Auth::user()->user;
        if ($user == "admin") {
            $res = Template_2G::destroy($id);
            if ($res) {
                echo "1";
            } else {
                echo "2";
            }
        } else {
            $res = Template_2G::where('id', $id)->where('user', $user)->delete();
            if ($res) {
                echo "1";
            } else {
                echo "3";
            }
        }

    }//end deleteMode()


    /**
     * 复制模板
     *
     * @return void
     */
    public function copyMode()
    {
        $user      = Auth::user()->user;
        $id           = input::get("copyId");
        $templateName = input::get('modeName_copy');
        $description  = input::get('modeDescription_copy');

        $result = Template_2G::where('templateName', $templateName)->where('user', $user)->count();
        if ($result != 0) {
            echo "名称已有";
            return;
        }
        $elementId = Template_2G::where('id', $id)->first()->elementId;
        $newTemplate_2G = new Template_2G;
        $newTemplate_2G->templateName = $templateName;
        $newTemplate_2G->elementId = $elementId;
        $newTemplate_2G->description = $description;
        $newTemplate_2G->user = $user;
        $res =$newTemplate_2G->save();
        if ($res) {
            echo true;
        } else {
            echo false;
        }

    }//end copyMode()


}//end class
