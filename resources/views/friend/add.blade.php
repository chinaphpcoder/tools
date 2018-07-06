@extends('public.base')

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li><a href="{{route('friend_list')}}">合作机构</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('friend_list')}}" class="btn btn-primary active" role="button">返回列表</a>
                </div>
                <form class="form-horizontal" method="POST" action="{{route('friend_update')}}" role="form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">合作机构名称<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" placeholder="请输入名称" value="{{old('title')}}">
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
                                <div class="row {{ $errors->has('pc_index_pic') ? ' has-error' : '' }}">
                                    <div class="col-sm-4">
                                        pc端(首页展示) 234px*114px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="pc_index_pic" value="">
                                    </div>
                                    @if ($errors->has('pc_index_pic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('pc_index_pic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="row {{ $errors->has('pc_index_small_pic') ? ' has-error' : '' }}" style="margin-top:20px">
                                    <div class="col-sm-4">
                                        pc端(首页展示) 170px*84px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="pc_index_small_pic" value="">
                                    </div>
                                    @if ($errors->has('pc_index_small_pic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('pc_index_small_pic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="row {{ $errors->has('pc_cooperate_pic') ? ' has-error' : '' }}" style="margin-top:20px">
                                    <div class="col-sm-4">
                                        pc端(合作机构) 430px*218px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="pc_cooperate_pic" value="">
                                    </div>
                                    @if ($errors->has('pc_cooperate_pic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('pc_cooperate_pic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="row {{ $errors->has('pc_cooperate_small_pic') ? ' has-error' : '' }}" style="margin-top:20px">
                                    <div class="col-sm-4">
                                        pc端(合作机构) 314px*160px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="pc_cooperate_small_pic" value="">
                                    </div>
                                    @if ($errors->has('pc_cooperate_small_pic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('pc_cooperate_small_pic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="row {{ $errors->has('m_pic') ? ' has-error' : '' }}" style="margin-top:20px">
                                    <div class="col-sm-4">
                                        M站 / app 208px*106px
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="m_pic" value="">
                                    </div>
                                    @if ($errors->has('m_pic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('m_pic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">描述<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="description" rows="3" placeholder="300字以内">{{old('description')}}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('description') }}</strong>
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