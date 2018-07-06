@extends('public.base')

@section('content')
    <section class="content-header">
        <h1>
            {{$meta_title}}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
            <li><a href="{{route('story_index', ['type' => $type])}}">小沙故事</a></li>
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <a href="{{route('story_index', ['type' => $type])}}" class="btn btn-default" type="button">返回列表</a>
                    </div>
                    <form id="form" method="POST" action="{{route('story_update', ['type' => $type])}}" role="form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                <label>文章标题</label>
                                <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title', $row->title) }}">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label>标签：</label>
                                <input type="radio" name="attr" value="3" {{ $row->attr == 3 ? 'checked' : '' }}> &nbsp;普通&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="attr" value="1" {{ $row->attr == 1 ? 'checked' : '' }}> &nbsp;置顶&nbsp;&nbsp;
                                <input type="radio" name="attr" value="2" {{ $row->attr == 2 ? 'checked' : '' }}> &nbsp;精品&nbsp;&nbsp;
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label>是否显示：</label>
                                <input type="radio" name="status" value="1" {{ $row->status != 2 ? 'checked' : ''}}> 显示
                                <input type="radio" name="status" value="2" {{ $row->status == 2 ? 'checked' : ''}}> 隐藏
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <input type="checkbox" name="thumb" id="thumb" value="1" {{ $row->pic ? 'checked' : ''}}>
                                是否上传缩略图
                                <input type="file" name="pic" class="form-control" {{ $row->pic ? '' : 'style=display:none'}}>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                                <label>文章内容</label>
                                <textarea class="form-control" name="content" id="editor" rows="10" cols="80">{{ old('content', $row->content) }}</textarea>
                                @if ($errors->has('content'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('content') }}</strong>
                                </span>
                                @endif
                            </div>
                            <p style="text-align: right">阅读数：{{ $row->views }} &nbsp;&nbsp;点赞数：{{ $row->thumb_up }} &nbsp;&nbsp;评论数：{{ $row->comments()->where('comment_id', 0)->count() }} &nbsp;&nbsp;<a href="javascript:;" onclick="layShow('{{ route('story_message', ['id' => $row->id]) }}')">查看评论</a></p>
                        </div>
                        <div class="box-footer">
                            <input type="hidden" name="id" value="{{ $row->id }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            <button id="save" type="button" class="btn btn-primary">暂存</button>
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
    <script src="{{ url('js/layer/layer.js') }}"></script>
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

                //确认提交
                var status = confirm('您确定要提交吗？');
                if (!status) {
                    return false;
                }

                $('form').submit();
            })
            //保存
            $('#save').click(function(){
                $('#form').attr('action', $('#form').attr('action') + '&status=3');

                //确认提交
                var status = confirm('您确定要提交吗？');
                if (!status) {
                    return false;
                }

                $('form').submit();
            });

            // 是否允许上传缩略图
            $('#thumb').click(function() {
                var is_thumb = $('#thumb').is(':checked');
                if (is_thumb == true) {
                    $("input[name=pic]").css('display', 'block');
                } else {
                    $("input[name=pic]").css('display', 'none');
                }
            })
        });
        //查看评论弹层
        function layShow(url, title) {
            layer.open({
                type: 2,
                title: title,
                shadeClose: true,
                shade: 0.8,
                area: ['600px', '70%'],
                content: url
            });
        }
    </script>
@endsection