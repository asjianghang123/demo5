@extends('layouts.nav')
@section('content-header')
<section class="content-header">
  <h1>SC分场景核查<!-- <small>advanced tables</small> --></h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-dashboard"></i>日常优化
    </li>
    <li>参数分析
    </li>
    <li class="active">系统常量检查</li>
  </ol>
</section>
@endsection
@section('content')

<section class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-header with-border">
                    <div style="display:inline">
                        <h3 class="box-title">
                        <form class="form-inline">
                            <div class="form-group">
                                日期：
                            </div>
                            <div class="form-group">
                                <label class="sr-only"></label>
                                <p class="form-control-static">
                                    
                                    <select id="date" class="form-control input-sm" style="width:180px;">
                                    </select> 
                                    
                                </p>                    
                            </div>
                            <div class="form-group">
                            城市：
                            </div>
                            <div class="form-group">
                                <label class="sr-only"></label>
                                <p class="form-control-static">
                                    
                                    <select id="cityList" class="form-control " multiple="multiple">
                                    </select>
                                    
                                </p>                    
                            </div>
                        </form>

                        </h3>
                                  
                    </div>

                    <div class="input-group" style="padding-top:10px;float:right;display:inline">
                        <a id="import" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importTemplate()"><span class="ladda-label">导入</span></a>
                        <a id="exportTemplate" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportTemplate()"><span class="ladda-label">导出模板</span></a> 
                        <a id="run" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="run()"><span class="ladda-label">运行</span></a>
                        <a id="query" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doQuery()"><span class="ladda-label">查询</span></a>
                        <a id="exportSystemConstantsCheck" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportSystemConstantsCheck()"><span class="ladda-label">导出</span></a>
                       <!--  <a id="exportOriginalConfig" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportOriginalConfig()"><span class="ladda-label">导出倒回配置</span></a> -->
                    </div>
                </div>
                <div class="box-body">
                    <table id="systemConstantsCheckTable">
                    
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- 导入弹出框 -->
<div class="modal fade" id="import_modal">
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
                       <input type="file" accept=".csv" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
                       <span class="input-group-btn">
                          <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
                       </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()">确定</button>
                <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<link type="text/css" rel="stylesheet" href="dist/css/button.css" >
<script type="text/javascript" src="dist/js/parameterAnalysis/scCheck.js"></script>

@endsection


