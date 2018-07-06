@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="">菜单管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$meta_title}}</h3>
                </div>
                <form method="POST" action="{{route('menu_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label>名称</label>
                            <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title') }}">
                            @if ($errors->has('title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('sort') ? ' has-error' : '' }}">
                            <label>排序值</label>
                            <input type="number" class="form-control" name="sort" placeholder="请输入排序值[数值越高，优先级越高]" value="{{ old('sort') }}">
                            @if ($errors->has('sort'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sort') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
                            <label>URL</label>
                            <input type="text" class="form-control" name="url" placeholder="请输入URL" value="{{ old('url') }}">
                            @if ($errors->has('url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('url') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('tip') ? ' has-error' : '' }}">
                            <label>提示</label>
                            <input type="text" class="form-control" name="tip" placeholder="请输入提示" value="{{ old('tip') }}">
                            @if ($errors->has('tip'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tip') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('group') ? ' has-error' : '' }}">
                            <label>分组</label>
                            <input type="text" class="form-control" name="group" placeholder="请输入分组" value="{{ old('group') }}">
                            @if ($errors->has('group'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('group') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                            <label>状态</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="status" value="1" checked="">显示
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="status" value="2">隐藏
                                </label>
                            </div>
                            @if ($errors->has('status'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('status') }}</strong>
                                </span>
                            @endif
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
