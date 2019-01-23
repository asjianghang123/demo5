@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>查看全部通知</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>通知
		</li>
		<li class="active">查看全部通知</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-body">
					<h4>
			            全部通知
			        </h4>
			        <ul class="list-group" id="noticeList" style="padding-top:10px">
			        </ul>
	            </div>
			</div>
		</div>
	</div>
</section>

@endsection


@section('scripts')
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/readAllNotice.js"></script>

