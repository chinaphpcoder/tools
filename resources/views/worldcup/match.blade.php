@extends('public.base')

@section('css')
    <link rel="stylesheet" href="//cdn.bootcss.com/datatables/1.10.15/css/dataTables.bootstrap.min.css">
    <style type="text/css">
        th{
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>
            {{$meta_title}}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
            <li><a href="javascript:;">赛制管理</a></li>
            <li class="active"></li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <a href="{{ route('world_add_match') }}" class="btn btn-default">新增赛制</a>

                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th align="center">序号</th>
                                    <th align="center">赛制</th>
                                    <th align="center">比赛时间</th>
                                    <th align="center">比赛球队1</th>
                                    <th align="center">比赛球队2</th>
                                    <th align="center">比赛结果</th>
                                    <th align="center">操作</th>
                                </tr>
                                </thead>
                                <tbody id="list">
                                @foreach ($data as $row)
                                    <tr>
                                        <td align="center">{{ $row['id'] }}</td>
                                        <td align="center">{{ $row['type'] }}</td>
                                        <td align="center">{{ $row['match_time'] }}</td>
                                        <td align="center">{{ $row['team1'] }}</td>
                                        <td align="center">{{ $row['team2'] }}</td>
                                        <td align="center">{{ $row['result'] }}</td>
                                        <td align="center">
                                            <a class="btn btn-danger" href="{{ route('world_match_edit', ['id' => $row['id']]) }}">编辑</a>
                                            <a class="btn btn-danger row-delete" href="{{ route('world_match_delete', ['id' => $row['id']]) }}">删除</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ url('js/layer/layer.js') }}"></script>
    <script type="text/javascript">
        //单个删除
        $('.row-delete').click(function(){
            //删除确认
            var status = confirm('您确定要删除吗？');
            if (!status) {
                return false;
            }
        })
    </script>

@endsection