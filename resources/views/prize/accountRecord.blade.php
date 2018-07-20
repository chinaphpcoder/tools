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
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <table id="test" class="layui-table" lay-filter="demo" style="width: 90%"></table>
    </section>
@endsection

@section('javascript')

<script src="{{ asset('layui/layui.js') }}" charset="utf-8"></script>
 
<script>
layui.use('table', function(){
  var table = layui.table;

  //监听工具条
  table.on('tool(demo)', function(obj){
    var data = obj.data;
    console.log(data);
    if(obj.event === 'edit'){
        layer.open({
            type: 2,
            title: '修改中奖概率',
            shadeClose: true,
            shade: 0.8,
            area: ['350px', '200px'],
            scrollbar: false,
            content: ["{{ route('activity.prize.probability.edit') }}?id="+ data.id,'no'] //iframe的url
        });
    } else if(obj.event === 'detail'){
        layer.open({
            type: 2,
            title: '奖品详情',
            shadeClose: true,
            shade: 0.8,
            area: ['420px','600px'],
            scrollbar: false,
            content: ["{{ route('activity.prize.details') }}?id="+ data.id,'no'] //iframe的url
        });
    }else if(obj.event === 'del'){
        layer.msg('功能暂未开放');
    }
  });
  
  table.render({
    elem: '#test'
    ,url:'{{ route("activity.prize.prize_list") }}/{{ $identification }}'+'?is_test={{ $is_test }}'
    //,width: '100%' //全局定义常规单元格的最小宽度，layui 2.2.1 新增
    ,cellMinWidth: 100
    ,size:'lg'
    ,cols: [[
      {field:'pid', title: '序号'}
      ,{field:'admin_prize_name', title: '奖品名称'}
      ,{field:'obtain_probability',title: '中奖概率'}
      ,{field:'op', title: '操作',toolbar: '#barDemo'}
    ]]
  });
});
</script>

<script type="text/html" id="barDemo">
    @if($admin == 1)
    <a class="layui-btn layui-btn-primary layui-btn-sm" lay-event="detail">奖品属性</a>
    @endif
    <a class="layui-btn layui-btn-sm" lay-event="edit">设定概率</a>
    <!-- <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a> -->
</script>

@endsection