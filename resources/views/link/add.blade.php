@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('link_list')}}">友情链接</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('link_list')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{route('link_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">名称<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title') }}">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">上传logo<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="row {{ $errors->has('image_url') ? ' has-error' : '' }}" style="margin-top:20px">
                                    <div class="col-sm-4">
                                        pc端 196px*96px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="image_url" value="">
                                    </div>
                                    @if ($errors->has('image_url'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('image_url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="row {{ $errors->has('image_small_url') ? ' has-error' : '' }}" style="margin-top:20px">
                                    <div class="col-sm-4">
                                        pc端小尺寸 144px*70px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="image_small_url" value="">
                                    </div>
                                    @if ($errors->has('image_small_url'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('image_small_url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">网址<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="url" placeholder="请输入网址" value="{{ old('url') }}">
                                @if ($errors->has('url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('sort') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">排序值</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="sort" placeholder="请输入排序值[数值越高，优先级越高]" value="{{ old('sort', 0) }}">
                                @if ($errors->has('sort'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sort') }}</strong>
                                    </span>
                                @endif
                            </div>
                                
                        </div>
                    </div>
                    <div class="box-footer col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $('form').submit(function(){
        var status = confirm('您确定要提交吗？');
        if (!status) {
            return false;
        }
    })
</script>
@endsection