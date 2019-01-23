@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>使用帮助</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>服务
		</li>
		<li class="active">使用帮助</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-body">
					<ul class="list-group downloadList">
						<li class="list-group-item">
							<h4 class="list-group-item-heading">
					            用户使用手册
					            <div class="pull-right">
									<button type="button" class="btn btn-primary ladda-button" data-style="expand-right" onclick="downloadDoc()">
		                        		<span class="ladda-label">下载</span>
			                    	</button>
								</div>
					        </h4>
					        <p class="list-group-item-text">
					            勾选需要的文件进行下载
					        </p>
					        <ul class="list-group" id="downloadDoc" style="padding-top:10px">
					        </ul>
						</li>
						<li class="list-group-item">
							<h4 class="list-group-item-heading">
					            用户使用视频
					            <div class="pull-right">
									<button type="button" class="btn btn-primary ladda-button" data-style="expand-right" onclick="downloadVideo()">
		                        		<span class="ladda-label">下载</span>
			                    	</button>
								</div>
					        </h4>
					        <p class="list-group-item-text">
					            勾选需要的文件进行下载
					        </p>
					        <ul class="list-group" id="downloadVideo" style="padding-top:10px">
					        </ul>
						</li>
					</ul>
					
	            </div>
			</div>
		</div>
	</div>
</section>

@endsection


@section('scripts')

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style>
</style>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/downloadCourse.js"></script>

