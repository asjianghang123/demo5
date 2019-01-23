@extends('layouts.nav')
@section('content-header')
<section class="content-header">
  <h1>新建站参数检查</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-thumbs-up"></i>特色功能
    </li>
    <li>参数
    </li>
    <li class="active">新建站参数检查</li>
  </ol>
</section>
@endsection
@section('content')

<section class="content">
  <div class="row">
    <div class="col-sm-3"> 
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">任务列表</h3>
          <div class="pull-right box-tools">
            <button class="btn btn-primary btn-sm" onclick="downTemplate()">
              <span class="ladda-label">模板</span>
            </button>
          </div>
        </div>
        <div class="box-body">
            <div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
            <div id="storageQueryTree"></div>
          </div>
          <input type="hidden" value="" id="storageFlag">
        </div>
      </div>
    </div>
    <div class="col-sm-9">
        <div class="box">
            <div class="box-header">
              <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="box-tools">
                           <div class="btn-group">
                                <button type="button" class="btn  btn-primary" onclick="addRndCheckTask()">
                                  <i class="fa fa-plus"></i>新建
                              </button>
                            </div>
                            <div class="btn-group">
                                <button type="button" id="runRndCheckTask" class="btn btn-primary" onclick="runRndCheckTask()">
                                  <i class="fa fa-play"></i>启动
                              </button>
                            </div>
                             <div class="btn-group">
                                <button type="button" id="exportBtn" class="btn btn-primary" onclick="exportRndCheckTask()">
                                  <i class="fa fa-arrow-circle-down"></i>导出
                              </button>
                            </div>
                             <div class="btn-group">
                                <button type="button" id="deleteBtn" class="btn" onclick="deleteTask()">
                                  <i class="fa fa-close"></i>删除
                              </button>
                            </div>
                        </div>    
              </div>
            </div>
            <div class="box">
                 <div class="box-header">
              日志
             </div>
            <div class="box-body">
                    <table id="RndCheckTaskTable">
                    </table>
             </div>
            </div>
          
          </div>
    </div>
  </div>
</section>
<!-- 统计信息弹出框 -->
<!-- <div class="modal fade" id="import_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">导入</h8>
            </div>
      <div class="modal-body col-sm-12">
          
        <input type="hidden" name="siteSign" id="siteSign" value="">
        <div class="col-sm-8 col-sm-offset-2">
          <div class="input-group">
                   <input type="text" class="form-control" id="fileImportName">
                   <input type="file" accept=".xlsx" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
                   <span class="input-group-btn">
                      <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
                   </span>
                </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()" >确定</button>
        <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div> -->
<div class="modal fade " id="template">
  <div class="modal-dialog"> 
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
          </button>
          <h8 class="modal-title">模板下载</h8>
        </div>
        <div class="modal-body text-center">
           <div class="col-sm-2">
        
      </div>
          <div class="col-sm-4">
                 <button class="btn btn-primary" onclick="exportTemplate('TDD')"><span class="">TDD模板</span>          </button>
          </div>
          <div class="col-sm-4">
             <button class="btn btn-primary" onclick="exportTemplate('FDD')"><span class="">FDD模板</span>          </button>
          </div>
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>
  
</div>
<div class="modal fade" id="addRndCheckTask">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="close">
            </button>
            <h8 class="modal-title">添加任务</h8>
          </div>
          <form class="form-horizontal" role="form" id="taskForm">
            <div class="modal-body text-center">
              <div class="form-group">
                 <label for="city" class="col-sm-2  col-sm-offset-2 control-label">城市</label>
                <div class="col-sm-6">
                  <select id="city" class="form-group input-sm">
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="taskName" class="col-sm-2 col-sm-offset-2 control-label ">任务名称</label>
                <div class="col-sm-6">
                  <input type="text" name="taskName" id="taskName" class="form-control">
                  
                </div>
              </div>
              <div class="form-group">
                <label for="modeDescription" class="col-sm-2 col-sm-offset-2 control-label">
                  小区类型
                </label>
                <div class="col-sm-6">
                     <input id='cellsType'  data-toggle="toggle" data-on="FDD" data-off="TDD" data-onstyle="info" data-offstyle="success" data-width="100" data-size="mini" type="checkbox">
                    </div>
               </div>
               <div class="form-group">
               <label for="modeDescription" class="col-sm-2 col-sm-offset-2 control-label">
                    选择文件
                </label>
              <div class="col-sm-6">
                <div class="input-group">
                         <input type="text" class="form-control" id="fileImportName">
                         <input type="file" accept=".csv" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
                         <span class="input-group-btn">
                            <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
                         </span>
                      </div>
              </div>
            </div>
              <div class="modal-footer">
              <button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="addTask()">保存</button>
              <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
            </div>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
@endsection

@section('scripts')


<link type="text/css" rel="stylesheet" href="plugins/bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.css">
<script type="text/javascript" src="plugins/bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style type='text/css'>
  .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
  } 
</style>

<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--bootatrap-toggle-->
<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>

@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/SpecialFunction/RNDcheck.js"></script>

