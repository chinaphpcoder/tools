@extends('public.base')

@section('css')
<link href="//cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
@endsection

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('carousel_list')}}">轮播图</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{route('createTeam')}}" role="form">
                    {{ csrf_field() }}

                    <input type="hidden" value="{{$data['id']}}}" name="id">

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">球队队名<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="team_name" placeholder="请输入名称" value="{{ $data['team_name'] }}">
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('image_url') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">球队队标<span class="text-danger"></span></label>
                            <div class="col-sm-10">
                                <!--<div style="border: 1px solid;border-radius: 5px;text-align: center;padding: 10px;width: 120px;">点击上传</div>-->
                                @if( empty($data['pic']) )
                                    <input type="file" name="file" >
                                @endif
                                @if( !empty($data['pic']) )
                                    <div style="border: 1px solid;border-radius: 5px;text-align: center;padding: 10px;width: 120px;">点击上传</div>
                                    <input type="file" name="file" style="opacity: 0; position: absolute; top:0; left: 0;padding: 10px;width: 120px;">
                                @endif
                                <input type="hidden" value="{{$data['pic']}}" name="team_pic">
                                @if( !empty($data['pic']) )
                                    <img src="{{$data['pic']}}" style="width: 22%;">
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="box-footer col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script src="//cdn.bootcss.com/moment.js/2.18.1/moment.min.js"></script>
<script src="//cdn.bootcss.com/moment.js/2.18.1/locale/zh-cn.js"></script>
<script src="//cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
$(function(){
    //表单提交确认
    $('form').submit(function(){
        var status = confirm('您确定要提交吗？');
        if (!status) {
            return false;
        }
    })

    //时间插件显示
    $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        sideBySide: true
    });
    $('#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        sideBySide: true
    });
})
</script>
@endsection