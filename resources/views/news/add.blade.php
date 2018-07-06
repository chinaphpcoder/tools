@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('news_list')}}">媒体报道</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('news_list')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form class="form-horizontal" id="form" method="POST" action="{{route('news_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">文章标题*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title') }}">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('type_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">文章类型*</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="type_id">
                                    <option value="0">请选择文章类型</option>
                                    @foreach($type as $row)
                                        <option <?=(old('type_id') == $row->id ? 'selected="selected"' : '')?> value="{{$row->id}}">{{$row->title}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('type_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('type_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('source_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">文章来源*</label>
                            <div class="col-md-4">
                                <select id="article-source" class="form-control" name="source_id">
                                <option value="0">请选择文章来源</option>
                                @foreach($source as $row)
                                    <option <?=(old('source_id') == $row->id ? 'selected="selected"' : '')?> data-url="{{$row->image_url}}" value="{{$row->id}}">{{$row->title}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('source_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('source_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <img id="article-source-image" src="">
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">文章内容*</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="content" id="editor" rows="10" cols="80">{{ old('content') }}</textarea>
                                @if ($errors->has('content'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('content') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button id="save" type="button" class="btn btn-primary  col-sm-offset-2">保存</button>
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
    });

    //显示图片
    function show_source(){
        var url = $('#article-source').find("option:selected").data('url');

        if (url) {
            $('#article-source-image').attr('src', url);
        } else {
            $('#article-source-image').attr('src', '');
        }
    }

    //文章来源显示图片
    $('#article-source').change(function(){
        show_source();
    })
    show_source();
})
</script>
@endsection