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
            content: ["{{ route('probability_setting') }}?id="+ data.id,'no'] //iframe的url
        });
        // layer.open("{{ route('probability_setting') }}?id=data.id")
      // layer.alert('编辑行：<br>'+ JSON.stringify(data))
    }
  });
  
  table.render({
    elem: '#test'
    ,url:'{{ route("dragonboat_prize_list") }}'+'?is_test={{ $is_test }}'
    //,width: '100%' //全局定义常规单元格的最小宽度，layui 2.2.1 新增
    ,cellMinWidth: 100
    ,size:'lg'
    ,cols: [[
      {field:'pid', title: '序号'}
      ,{field:'prize_name', title: '奖品名称'}
      ,{field:'obtain_probability',title: '中奖概率'}
      ,{field:'op', title: '操作',toolbar: '#barDemo'}
    ]]
  });
});
</script>

<script type="text/html" id="barDemo">
  <a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="edit">编辑</a>
</script>

@endsection