@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('type_list', ['type' => $type_id])}}">法律法规</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('type_list', ['type' => $type_id])}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form method="POST" action="{{route('type_update', ['type' => $type_id])}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <input type="hidden" name="id", value="{{$row->id}}">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label>名称</label>
                            <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{ old('title', $row->title) }}">
                            @if ($errors->has('title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
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