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
        <li><a href="{{route('legal_list')}}">法律法规</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('legal_list')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form method="POST" action="{{route('legal_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label>名称<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title') }}">
                            @if ($errors->has('title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('url_pc') ? ' has-error' : '' }}">
                            <label>网址（PC）<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="url_pc" placeholder="请输入网址" value="{{ old('url_pc') }}">
                            @if ($errors->has('url_pc'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('url_pc') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('url_mobile') ? ' has-error' : '' }}">
                            <label>网址（M站）<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="url_mobile" placeholder="请输入网址" value="{{ old('url_mobile') }}">
                            @if ($errors->has('url_mobile'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('url_mobile') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        <div class="form-group {{ $errors->has('published_at') ? ' has-error' : '' }}">
                            <label>发布时间<span class="text-danger">*</span></label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input type='text' name="published_at" placeholder="请选择时间" class="form-control" value="{{ old('published_at') }}" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                @if ($errors->has('published_at'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('published_at') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">提交</button>
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
    $(function () {
        //时间插件 显示
        $('#datetimepicker1').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            sideBySide: true
        });

        //表单确认提交
        $('form').submit(function(){
            var status = confirm('您确定要提交吗？');
            if (!status) {
                return false;
            }
        })
    });
</script>
@endsection