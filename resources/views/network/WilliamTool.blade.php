@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        冗余站点清除
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 网络规划</li>
      {{--   <li>第二级菜单</li> --}}
        <li class="active">WilliamTool对接 </li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div class="row">
      <div class="col-sm-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">查询条件</h3>
          </div>
          <div class="box-body">
            <div class="col-sm-3">
              <div class="form-group">
                  <label for="kgetDate">日期</label>
                  <select id="kgetDate" class="js-example-basic-single js-states form-group col-xs-10">
                  </select>
                </div>
            </div>
              <div class="col-sm-2">
                
              </div>
          </div>

        </div>
      </div>
        <div class="col-sm-12">
             <div class="box">
                 <div class="box-header with-border">
                     <div style="display:inline;">
                         <h3 class="box-title">LTE_Carrier信息</h3>
                     </div>
                    <div class="input-group" style="float: right;display: inline;">
                       <a id="queryCarrier" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="queryCarrierData()"><span class="ladda-label ">查询</span></a>
                          <a id="importCarrier" class="btn btn-primary ladda-button" data-style="expand-right"  onClick="importCarrierData()" href="#"><span class="ladda-label">导出</span></a>
                        
                    </div>

                 </div>

                <div class="box-body">
                    <table id="CarrierTable">
                        
                    </table>
                </div>

             </div>
              <div class="box">
                 <div class="box-header with-border">
                     <div style="display:inline;">
                         <h3 class="box-title">LTE_Neighbor信息</h3>
                     </div>
                    <div class="input-group" style="float: right;display: inline;">
                      <a id="queryNeighbor" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="queryNeighborData()"><span class="ladda-label ">查询</span></a>
                          <a id="importNeighbor"  class="btn btn-primary ladda-button" onClick="importNeighborData()" href="#" data-style="expand-right"><span class="ladda-label">导出</span></a>
                        
                    </div>

                 </div>

                <div class="box-body">
                    <table id="NeighborTable">
                        
                    </table>
                </div>

             </div>
         </div>
  </div>
</section>




@endsection
@section('scripts')
<!-- grid -->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
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
<script type="text/javascript" src="dist/js/genius/WilliamTool.js"></script>

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

