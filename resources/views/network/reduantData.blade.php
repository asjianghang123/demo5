@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        冗余数据清除
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 日常优化</li>
        <li>参数分析</li>
        <li class="active">冗余数据清除 </li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
     <div class="row">
        <div class="col-sm-3">
            <div class="box">
                <div class="box-header with-border">
                    <div style="display: inline; float: left;">
                        <h3 class="box-title">要删除站点信息</h3>
                    </div>
                      
                    <div class="input-group" style="float: right;display: inline;">
                      
                        <a id="importTemplate" class="btn btn-primary ladda-button" href="#" onclick="importTemplate()"><span class="ladda-label">导入</span></a>
                        <a id="exportTemplate" class="btn btn-primary ladda-button" href="#" onclick="exportTemplate()"><span class="ladda-label">模板</span></a>

                    </div>
                </div>
        
            </div>
        </div>
         <div class="col-sm-9">
             <div class="box">
                 <div class="box-header with-border">
                     <div style="display:inline;">
                         <h3 class="box-title">详细信息</h3>
                     </div>
                    <div class="input-group" style="float: right;display: inline;">
                          <a id="import" class="btn btn-primary ladda-button" href="#" onclick="importKPI()"><span class="ladda-label">导出</span></a>
                        
                    </div>

                 </div>

                <div class="box-body">
                    <table id="KPITable">
                        
                    </table>
                </div>

             </div>
              <div class="box">
                 <div class="box-header with-border">
                     <div style="display:inline;">
                         <h3 class="box-title">4G邻区信息</h3>
                     </div>
                    <div class="input-group" style="float: right;display: inline;">
                          <a id="import" class="btn btn-primary ladda-button" href="#" onclick="import4G()"><span class="ladda-label">导出</span></a>
                        
                    </div>

                 </div>

                <div class="box-body">
                    <table id="4GTable">
                        
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
        <button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()" >确定</button>
        <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>


@endsection
@section('scripts')

<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/genius/reduantData.js"></script>

<style>
#siteTable td div{
        width:100%;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
a.btn {
  display: inline-block;
  padding: 4px 8px;
  margin-bottom: 0;
  font-size: 13px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
      touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}
</style>

