@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        信令概览
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 信令概览</li>
        <li>信令概览</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">NAS信令指标</h3>
                </div>
                <div class="box-body"></div>
                <!-- <div class="chart tab-pane active" id="chart-access" style="position: relative;height: 400px;"></div> -->
            </div>
         </div>
    </div>            
</section>
@endsection
@section('scripts')
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="dist/js/genius/singal-chart.js"></script>
@endsection



