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
            <li><a href="javascript:;">欢乐大转盘</a></li>
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <form class="form-inline" action="" method="post">
                                {!! csrf_field() !!}
                                <div class="form-group" style="margin-left: 10px;">
                                    <label>用户ID</label>
                                    <input type="text" class="form-control" name="user_id" placeholder="请输入用户ID">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label >手机号：</label>
                                    <input type="text" class="form-control" name="mobile" placeholder="请输入手机号">
                                </div>
                                <div class="form-group"  style="margin-left: 10px;">
                                    <label>奖品：</label>
                                    <select name="prize_id" id="" class="form-control">
                                        <option value="0">全部</option>
                                        @foreach ($prize as $val)
                                        <option value="{{ $val['id'] }}">{{ $val['prize_name'] }}</option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label>奖品状态：</label>
                                    <select name="state" id="" class="form-control">
                                        <option value="3">全部</option>
                                        <option value="0">未获得</option>
                                        <option value="1">预计获得</option>
                                        <option value="2">已失效</option>
                                    </select>
                                </div>


                                <button type="submit" class="btn btn-default">查询</button>
                            </form>
                            <div style="clear: both;"></div>
                            <div style="margin-top: 20px;">
                                <a href="{{ route('user_export') }}" class="btn btn-default" type="button">导出EXCEL</a>
                            </div>
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover tb">
                                <tr>
                                    <td rowspan="2" style="vertical-align: middle; font-weight: bold;color: #000;">序号</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">用户ID</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">姓名</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">手机号</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">累计年出借金额</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">奖品</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">获得条件</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">奖品状态</td>
                                    <td colspan="3" style="font-weight: bold;">收货地址</td>
                                </tr>
                                <tr>
                                    <td>姓名</td>
                                    <td>电话</td>
                                    <td>收货地址</td>
                                </tr>
                                @forelse($data as $key => $val)
                                <tr>
                                    <td style="vertical-align: middle;">{{ $val['num'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['user_id'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['real_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['mobile'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['total'] }}</td>
                                    <td class="border_bottom" style="padding: 0;">
                                        @forelse($val['prize'] as $item)
                                            <p>{{ $item['prize_name'] }}</p>
                                        @empty
                                            <p>暂无</p>
                                        @endforelse
                                    </td>
                                    <td class="border_bottom" style="padding: 0;">
                                        @forelse($val['prize'] as $item)
                                            <p>{{ $item['detail'] }}</p>
                                        @empty
                                            <p>暂无</p>
                                        @endforelse
                                    </td>
                                    <td class="border_bottom" style="padding: 0;">
                                        @forelse($val['state'] as $item)
                                            <p>{{ $item }}</p>
                                        @empty
                                            <p>暂无</p>
                                        @endforelse
                                    </td>
                                    <td style="vertical-align: middle;">{{ $val['name'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['phone'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['address'] }}</td>
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