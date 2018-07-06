@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('college_list')}}">小沙学院</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('college_list')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form id="form" method="POST" action="{{route('college_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label>文章标题</label>
                            <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title') }}">
                            @if ($errors->has('title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('type_id') ? ' has-error' : '' }}">
                            <label>文章类型</label>
                            <select class="form-control" name="type_id">
                                <option value="0">请选择文章类型</option>
                                @foreach($type as $row)
                                    <option <?=($row->id == old('type_id') ? 'selected="selected"' : '')?> value="{{$row->id}}">{{$row->title}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('type_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('type_id') }}</strong>
                                </span>
                            @endif
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
})
</script>
@endsection