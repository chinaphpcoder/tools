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
            <li><a href="javascript:;">球队管理</a></li>
            <li class="active"></li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <a href="{{ route('add_team') }}" class="btn btn-default">新增球队</a>
                            <!-- <a href="javascript:;" class="btn btn-default" onclick="addExcel()" type="button">新增球队</a>-->
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th align="center">序号</th>
                                    <th align="center">球队队标</th>
                                    <th align="center">球队队名</th>
                                    <th align="center">操作</th>
                                </tr>
                                </thead>
                                <tbody id="list">
                                @foreach ($data as $row)
                                    <tr>
                                        <td align="center">{{ $row['id'] }}</td>
                                        <td align="center">{{ $row['pic'] }}</td>
                                        <td align="center">{{ $row['team_name'] }}</td>
                                        <td align="center">
                                            <a class="btn btn-danger" href="{{ route('world_team_edit', ['id' => $row['id']]) }}">编辑</a>
                                            <a class="btn btn-danger row-delete" href="{{ route('world_team_delete', ['id' => $row['id']]) }}">删除</a>
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