<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <meta name="business_identity_id" content="{{ $business_identity_id }}"/>
    <title>对账错误数据</title>
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}"  media="all">
</head>
<body>
    <table id="test" class="layui-table" lay-filter="demo" style="width: 90%"></table>
</body>

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
                    content: ["{{ route('finance.account-record-details') }}?id="+ data.id,'no'] //iframe的url
                });
                layer.full(index);
            } else if(obj.event === 'show-all-data'){
                var index = layer.open({
                    type: 2,
                    title: '查看详情',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['600px','650px'],
                    scrollbar: false,
                    content: ["{{ route('finance.account-record-details') }}?id="+ data.id,'no'] //iframe的url
                });
                layer.full(index);
            } else if(obj.event === 'show-error-data'){
                var index = layer.open({
                    type: 2,
                    title: '查看详情',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['600px','650px'],
                    scrollbar: false,
                    content: ["{{ route('finance.show-error-data') }}?id="+ data.id,'no'] //iframe的url
                });
                layer.full(index);
            }
        });
      
        table.render({
            elem: '#test'
            ,url:'{{ route("finance.get-account-record") }}'
            //,width: '100%' //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,cellMinWidth: 100
            ,size:'lg'
            ,cols: [[
                {field:'pid', title: '序号'}
                ,{field:'business_identity', title: '业务标识'}
                ,{field:'status_text', title: '状态'}
                ,{field:'created_at',title: '创建时间'}
                ,{field:'name',title: '创建人'}
                ,{field:'op', title: '操作',toolbar: '#barDemo'}
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


</script>

