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
        <table id="test" class="layui-table" lay-filter="weixin-reply" style="width: 90%"></table>
    </section>
@endsection

@section('javascript')

<script src="{{ asset('layui/layui.js') }}" charset="utf-8"></script>
 
<script>
layui.use('table', function(){
  var table = layui.table;

  //监听工具条
  table.on('tool(weixin-reply)', function(obj){
    var data = obj.data;
    console.log(data);
    if(obj.event === 'edit'){
        layer.open({
            type: 2,
            title: '设置回复内容',
            shadeClose: true,
            shade: 0.8,
            area: ['600px', '200px'],
            scrollbar: false,
            content: ["{{ route('weixin.reply_setting') }}?id="+ data.id,'no'] //iframe的url
        });
        // layer.open("{{ route('probability_setting') }}?id=data.id")
      // layer.alert('编辑行：<br>'+ JSON.stringify(data))
    }
  });
  
  table.render({
    elem: '#test'
    ,url:'{{ route("weixin.reply_list") }}'
    //,width: '100%' //全局定义常规单元格的最小宽度，layui 2.2.1 新增
    ,cellMinWidth: 100
    ,size:'lg'
    ,cols: [[
      {field:'pid', title: '序号'}
      ,{field:'label', title: '标签'}
      // ,{field:'content',title: '回复内容'}
      ,{field:'content',title: '回复内容',event: 'setContent'}
      ,{field:'started_at',title: '开始时间'}
      ,{field:'ended_at',title: '结束时间'}
      //,{field:'op', title: '操作',toolbar: '#barDemo'}
    ]]
  });
    //监听单元格事件
  table.on('tool(weixin-reply)', function(obj){
    var data = obj.data;
    if(obj.event === 'setContent'){
      layer.prompt({
        formType: 2
        ,area: ['600px', '300px']
        ,title: '编辑回复内容'
        ,value: data.content
      }, function(value, index){
        layer.close(index);

        $.ajax({
                type: 'post',
                headers: { 'X-CSRF-Token' : "{{csrf_token()}}" },
                data: {id:obj.data.id,content:value},
                url :"{{ route('weixin.reply_update') }}",
                success: function(data) {
                    if(data.code=="200"){
                        layer.msg("保存成功");
                        parent.location.reload();
                    } else {
                        layer.alert("保存失败");
                    }
                },
            })

      });
    }
  });
});

</script>

<script type="text/html" id="barDemo">
  <a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="edit">编辑</a>
</script>

@endsection