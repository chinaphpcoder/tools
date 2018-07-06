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

                                <button type="submit" class="btn btn-default">查询</button>
                            </form>
                            <div style="clear: both;"></div>
                            <div style="margin-top: 20px;">
                                <a href="{{ route('user_export') }}" class="btn btn-default" type="button">导出EXCEL</a>
                            </div>
                        </div>
                        <div class="box-body box-body-list">
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