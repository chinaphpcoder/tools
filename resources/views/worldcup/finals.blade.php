@extends('public.base')

@section('css')
    <link rel="stylesheet" href="//cdn.bootcss.com/datatables/1.10.15/css/dataTables.bootstrap.min.css">
    <style type="text/css">
        th{
            text-align: center;
        }
        .tb{
            text-align: center;
        }
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            border: 1px solid #ddd;
        }
        .border_bottom {
            padding: 0;
            margin: 0;
        }
        .border_bottom p {
            border-bottom: 1px solid #CCC;
            height: 40px;
            /*background: #efefef;*/
            line-height: 40px;
            font-size: 14px;
            padding: 0;
            display: block;
            margin: 0;
        }
        .border_bottom p:last-child {
            border-bottom: none;
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
            <li><a href="javascript:;">中奖用户管理</a></li>
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <a href="{{route('world_user_group')}}"><button type="button" class="btn btn-info">小组赛</button></a>
                        <a href="{{route('finals')}}"><button type="button" class="btn btn-info">决赛</button></a>
                        <div class="box-header">
                            <form class="form-inline" action="" method="get">
                                {!! csrf_field() !!}
                                <div class="form-group" style="margin-left: 10px;">
                                    <label>用户ID</label>
                                    <input type="text" class="form-control" name="user_id" placeholder="请输入用户ID">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label >手机号：</label>
                                    <input type="text" class="form-control" name="mobile" placeholder="请输入手机号">
                                </div>
                                <button type="submit" class="btn btn-default">查询</button>
                            </form>
                            <div style="clear: both;"></div>
                            <div style="margin-top: 20px;">
                                <a href="{{ route('finals_export') }}" class="btn btn-default" type="button">导出EXCEL</a>
                            </div>
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover tb">
                                <tr>
                                    <td rowspan="1" style="vertical-align: middle; font-weight: bold;color: #000;">序号</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">赛制</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">用户ID</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">姓名</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">手机号</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">只猜对球队场数</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">只猜对进第一球球队场数</td>
                                    <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">全部猜对场数</td>
                                </tr>

                                @forelse($list['data'] as $key => $val)
                                <tr>
                                    <td style="vertical-align: middle;">{{ $val['id'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['prize_ext5'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['user_id'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['real_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['mobile'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['count'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['first_count'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['sum'] }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" style="text-align: center">暂无任何中奖信息~</td>
                                </tr>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>
                {{$page->links()}}
            </div>
            <div class="row">

                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')

@endsection