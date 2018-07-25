@extends('public.base')

@section('css')
<link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}"  media="all">
@endsection

@section('content')
    <section class="content-header">
        <h1>
            {{$meta_title}}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
            <li class="active">数据记录</li>
        </ol>
    </section>
    <section class="content">
        <div class="layui-btn-group demoTable">
            <button class="layui-btn" data-type="getCheckData" onclick="export_all()">导出所有数据</button>
        </div>
        <table id="test" class="layui-table" lay-filter="demo" style="width: 90%"></table>
    </section>
@endsection

@section('javascript')

<script src="{{ asset('layui/layui.js') }}" charset="utf-8"></script>
 
<script>
    layui.use(['table','layer'], function(){
        var table = layui.table
            ,layer = layui.layer;
      
        table.render({
            elem: '#test'
            ,url:'{{ route("finance.get-data") }}?business_identity_id={{ $business_identity_id }}&type={{ $type }}'
            //,width: '100%' //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,cellMinWidth: 50
            ,size:'lg'
            ,cols: [[
                {field:'pid', title: '序号'}
                ,{field:'request_no', title: '流水号'}
                ,{field:'base_amount', title: '基准金额'}
                ,{field:'account_amount',title: '实际金额'}
                ,{field:'status_text',title: '对账状态'}
            ]]
            ,page: true
        });
    });
    function export_all() {
        var url = "{{ route('finance.export-data') }}?business_identity_id={{ $business_identity_id }}&type={{ $type }}";
        window.open(url,"_self");
    }

</script>

