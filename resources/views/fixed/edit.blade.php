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
        <li><a href="{{route('fixed_index')}}">固定图管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('fixed_index')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{route('fixed_update')}}" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$row->id}}">
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">名称<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title', $row->title) }}">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('pic') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">上传LOGO<span class="text-danger">*</span> pc端 750x320像素</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="pic" placeholder="">
                                @if ($errors->has('pic'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pic') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('display_position') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">显示位置<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <label class="checkbox-inline">
                                    <input type="checkbox"  name="display_position[]" value="recharge-result" {{ in_array("recharge-result",$positions) ? "checked='checked'" : "" }}>充值结果页
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="display_position[]" value="withdraw-result" {{ in_array("withdraw-result",$positions) ? "checked='checked'" : "" }}>提现结果页
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="display_position[]" value="invest-result" {{ in_array("invest-result",$positions) ? "checked='checked'" : "" }}>投资结果页
                                </label>
                                @if ($errors->has('display_position'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('display_position') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">链接地址<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="url" placeholder="请输入网址" value="{{ old('url', $row->url) }}">
                                @if ($errors->has('url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">开始时间<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' name="started_at" placeholder="请选择时间" class="form-control" value="{{ old('started_at', $row->started_at) }}" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    @if ($errors->has('started_at'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('started_at') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('ended_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">结束时间<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' id='datetimepicker2'>
                                    <input type='text' name="ended_at" placeholder="请选择时间" class="form-control" value="{{ old('ended_at', $row->ended_at) }}" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    @if ($errors->has('ended_at'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('ended_at') }}</strong>
                                        </span>
                                    @endif
                                </div>
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
</script>
@endsection