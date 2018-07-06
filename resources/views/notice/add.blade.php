@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('app_notice')}}">公告管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('app_notice')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form id="form" method="POST" action="{{route('app_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label>公告标题</label>
                            <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title') }}">
                            @if ($errors->has('title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label>开始时间<span class="text-danger">*</span></label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input type='text' name="started_at" placeholder="请选择时间" class="form-control" value="{{ old('started_at') }}" />
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
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('ended_at') ? ' has-error' : '' }}">
                            <label>结束时间<span class="text-danger">*</span></label>
                            <div class='input-group date' id='datetimepicker2'>
                                <input type='text' name="ended_at" placeholder="请选择时间" class="form-control" value="{{ old('ended_at') }}" />
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
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                            <label>文章内容</label>
                            <textarea class="form-control" name="content" id="editor" rows="10" cols="80">{{ old('content') }}</textarea>
                            @if ($errors->has('content'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('content') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="box-footer">
                        <button id="save" type="button" class="btn btn-primary">保存</button>
                        <button id="publish" type="button" class="btn btn-primary">马上发布</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script src="{{url('ckeditor/ckeditor.js')}}"></script>
<script src="//cdn.bootcss.com/moment.js/2.18.1/moment.min.js"></script>
<script src="//cdn.bootcss.com/moment.js/2.18.1/locale/zh-cn.js"></script>
<script src="//cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
$(function(){
    //显示本文编辑器
    CKEDITOR.replace('editor', {
        toolbar: 'uploadButton',
        defaultLanguage: 'zh-cn',
        extraPlugins: 'uploadimage,colorbutton,justify,lineheight', //,font
        uploadUrl : '{{route("upload_image")}}',
        scayt_sLang: 'zh-cn',
    });

    //发布
    $('#publish').click(function(){
        $('#form').attr('action', $('#form').attr('action') + '?status=1');

        //确认提交
        var status = confirm('您确定要提交吗？');
        if (!status) {
            return false;
        }

        $('form').submit();
    })
    //保存
    $('#save').click(function(){
        $('#form').attr('action', $('#form').attr('action') + '?status=0');

        //确认提交
        var status = confirm('您确定要提交吗？');
        if (!status) {
            return false;
        }
        
        $('form').submit();
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