@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('admin.activity_menu')}}">活动菜单管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('admin.activity_menu')}}" class="btn btn-primary active" role="button">返回列表</a>
                </div>
                <form class="form-horizontal" method="POST" action="{{route('admin.activity_update')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('activity_name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">活动名称<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="activity_name" placeholder="请输入活动名称" value="{{old('activity_name')}}">
                                @if ($errors->has('activity_name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('activity_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('identification') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">活动标识<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="identification" class="form-control" name="identification" placeholder="请输入活动标识" value="{{old('identification')}}">
                                @if ($errors->has('identification'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('identification') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
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