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
        <div class="layui-btn-group demoTable">
            <button class="layui-btn" data-type="getCheckData" onclick="show_add_record()">新增业务对账</button>
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

        //监听工具条
        table.on('tool(demo)', function(obj){
            var data = obj.data;
            if(obj.event === 'detail'){
                var index = layer.open({
                    type: 2,
                    title: '查看详情',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['600px','650px'],
                    scrollbar: false,
                    end: function(layero){
                        parent.location.reload();
                    },
                    content: "{{ route('finance.account-record-details') }}?id="+ data.id
                });
                layer.full(index);
            } else if(obj.event === 'del-record'){
                layer.confirm('确定删除该记录？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    var id = obj.data.id;
                    $.ajax({
                        url:'{{ route("finance.delete-account-record") }}',
                        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
                        type:'post',
                        data:{id: id},
                        dataType:'json',
                        success:function(data){

                            if(data.code == 200){
                                layer.msg(data.msg, {icon: 1,time:1000},function(){
                                parent.location.reload();
                            });
                                
                            }else{
                                layer.alert(data.msg);
                            }
                        },
                        
                        error:function(data){
                            layer.alert('系统异常');
                        },
                    });
                }, function(){
                });
            } else if(obj.event === 'show-all-data'){
                window.open("{{ route('finance.show-data') }}?id="+ data.id+'&type=0',"_blank");
            } else if(obj.event === 'show-error-data'){
                window.open("{{ route('finance.show-data') }}?id="+ data.id+'&type=1',"_blank");
            }
        });
      
        table.render({
            elem: '#test'
            ,url:'{{ route("finance.get-account-record") }}'
            //,width: '100%' //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,cellMinWidth: 20
            ,size:'lg'
            ,cols: [[
                {field:'pid', title: '序号',width: 60}
                ,{field:'business_identity', title: '业务标识'}
                ,{field:'business_alias', title: '业务名称'}
                ,{field:'status_text', title: '状态',minWidth: 80}
                ,{field:'created_at',title: '创建时间'}
                ,{field:'name',title: '创建人'}
                ,{field:'op', title: '操作',toolbar: '#barDemo',minWidth: 270}
            ]]
            ,page: true
        });
    });

    function add_record() {
        $.ajax({
            type: 'POST',
            url: '{{ route("finance.add-account-record") }}',
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
            dataType: 'json',
            data: {},
            success: function (data) {
                if( data.code == 200 ) {
                    layer.msg(data.msg, {icon: 1,time:1000},function(){
                                parent.location.reload();
                            });
                } else {
                    layer.alert(data.msg);
                }                 
            },
            error: function () {                    
                layer.alert('系统异常');
            }
        });
    }

    function show_add_record() {
        var index = layer.open({
                    type: 2,
                    title: '新增业务对账',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['400px','200px'],
                    scrollbar: false,
                    content: ["{{ route('finance.add-account-record') }}"]
                });
    }


</script>

<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-sm" lay-event="detail" style="margin-left: 0px">详情</a>
    <a class="layui-btn layui-btn-sm" lay-event="show-error-data" style="margin-left: 0px">错误数据</a>
    <a class="layui-btn layui-btn-sm" lay-event="show-all-data" style="margin-left: 0px">所有数据</a>
    <a class="layui-btn layui-btn-sm" lay-event="del-record" style="margin-left: 0px">删除</a>
</script>

@endsection
