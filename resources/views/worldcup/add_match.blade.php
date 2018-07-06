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
        <li><a href="{{route('world_team_match')}}">赛制管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{route('world_team_match')}}" class="btn btn-default" type="button">返回列表</a>
                </div>
                <form class="form-horizontal" method="POST" action="{{route('world_doadd_match')}}" role="form">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">赛制<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' style = "border:1px solid #000000;overflow:hidden;width:198px">
                                    <select name="type" class="match_change" style="width:100%">
                                        <option value="1">小组赛</option>
                                        <option value="2">1/8决赛</option>
                                        <option value="3">1/4决赛</option>
                                        <option value="4">半决赛</option>
                                        <option value="5">3、4名决赛</option>
                                        <option value="6">总决赛</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">开始时间<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' name="match_time" placeholder="请选择时间" class="form-control" value="{{ old('started_at') }}" />
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
                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">比赛球队1<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' style = "border:1px solid #000000;overflow:hidden;width:198px">
                                    <select name="team_id1" style="width:100%">
                                        @foreach($list as $row)
                                            <option value="{{$row['id']}}">{{$row['team_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">比赛球队2<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' style = "border:1px solid #000000;overflow:hidden;width:198px">
                                    <select name="team_id2" style="width:100%">
                                        @foreach($list as $row)
                                            <option value="{{$row['id']}}">{{$row['team_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label">比赛结果<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class='input-group date' style = "border:1px solid #000000;overflow:hidden;width:198px">
                                    <select name="result" style="width:100%">
                                        <option value="no" selected>未选择</option>
                                        <option value="team_id1">球队1</option>
                                        <option value="team_id2">球队2</option>
                                        <option value="dogfall">平局</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    <div class="form-group {{ $errors->has('started_at') ? ' has-error' : '' }} first" >
                        <label class="col-sm-2 control-label">进第一球队伍<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class='input-group date' style = "border:1px solid #000000;overflow:hidden;width:198px">
                                <select name="first" style="width:100%">
                                    <option value="no" selected>未选择</option>
                                    <option value="team_id1">球队1</option>
                                    <option value="team_id2">球队2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">保存</button>
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
$(function(){
    $('.first').hide();
    //表单提交确认
    $('form').submit(function(){
        var status = confirm('您确定要提交吗？');
        if (!status) {
            return false;
        }
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
    $('.match_change').change(function () {
        if($(this).val() > 1) {
            $(".first").show();
        } else {
            $(".first").hide();
        }
    });

})
</script>
@endsection