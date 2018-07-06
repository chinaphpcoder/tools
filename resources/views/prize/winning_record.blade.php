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
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <form id="prize-conditions" class="form-inline" action="" method="get">
                                <div class="form-group" style="margin-left: 10px;">
                                    <label>用户ID</label>
                                    <input type="text" class="form-control" name="user_id" placeholder="请输入用户ID" value="@if($conditions['user_id']) {{ $conditions['user_id'] }} @endif">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label >手机号：</label>
                                    <input type="text" class="form-control" name="mobile" placeholder="请输入手机号" value="@if($conditions['mobile']) {{ $conditions['mobile'] }} @endif">
                                </div>
                                <div class="form-group"  style="margin-left: 10px;">
                                    <label>奖品：</label>
                                    <select name="prize_id" id="" class="form-control">
                                        <option value="0">全部</option>
                                        @foreach ($prize as $val)
                                            <option value="{{ $val['id'] }}" 
                                                @if($conditions['prize_id'] == $val['id']) 
                                                    selected="selected" 
                                                @endif
                                            >
                                                {{ $val['admin_prize_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(isset($conditions['is_test']))
                                    <input type="hidden" name="is_test" value="{{ $conditions['is_test'] }}">
                                @endif
                                
                                <button type="submit" class="btn btn-default">查询</button>
                            </form>
                            <div style="clear: both;"></div>
                            <div style="margin-top: 20px;">
                                <a  onclick="export_all()" class="btn btn-default" type="button">全部导出</a>
                                <a  onclick="export_conditions()" class="btn btn-default" type="button">按筛选条件导出</a>
                            </div>
                                
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover tb">
                                <tr>
                                    <td rowspan="2" style="vertical-align: middle; font-weight: bold;color: #000;">序号</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">用户ID</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">姓名</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">手机号</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">累计出借金额</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">累计年出借金额</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">累计可抽奖次数</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">剩余抽奖次数</td>
                                    <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">奖品</td>
                                    <td colspan="3" style="font-weight: bold;">收货地址</td>
                                </tr>
                                <tr>
                                    <td>姓名</td>
                                    <td>电话</td>
                                    <td>收货地址</td>
                                </tr>
                                @forelse($user_prize_list as $key => $val)
                                <tr>
                                    <td style="vertical-align: middle;">{{ $val['num'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['user_id'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['real_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['mobile'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['total_invest_money'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['total_year_invest_money'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['total_draw_number'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['remain_draw_number'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['prize_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['consignee_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['consignee_mobile'] }}</td>
                                    <td style="vertical-align: middle;">{{ $val['consignee_address'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" style="text-align: center">暂无任何中奖信息~</td>
                                </tr>
                                @endforelse
                            </table>
                            
                        </div>
                        @if($page)
                            {{ $page->links() }}
                        @endif
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
<script type="text/javascript">
    function export_all() {
        var url = "{{ route('activity.prize.winning_record_export',$identification) }}" + @if(isset($conditions['is_test'])) '?is_test={{ $conditions['is_test'] }}' @else '' @endif;
        window.open(url,"_self");
    }
    function export_conditions() {
        var param = $("#prize-conditions").serialize();
        var url = "{{ route('activity.prize.winning_record_export',$identification) }}" + '?' + param;
        window.open(url,"_self");
    }
</script>
@endsection