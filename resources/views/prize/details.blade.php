<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <title>修改中奖概率</title>
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}"  media="all">
</head>
<body>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>获得条件</legend>
    </fieldset>
    <div class="layui-form">
        <table class="layui-table">
            <colgroup>
                <col width="100">
                <col width="200">
            </colgroup>
            <tbody>
            @foreach($obtain_conditions as $key=>$value)
            <tr>
                <td>{{ $value['key'] }}</td>
                <td>{{ $value['value'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if ($type == 0)
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>红包属性</legend>
        </fieldset>
        <div class="layui-form">
        <table class="layui-table">
            <colgroup>
                <col width="100">
                <col width="200">
            </colgroup>
            <tbody>
            @foreach($prize_attribute as $key=>$value)
            <tr>
                <td>{{ $value['key'] }}</td>
                <td>{{ $value['value'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
<script src="{{ asset('js/jquery-1.8.3.min.js') }}" charset="utf-8"></script>
<script src="{{ asset('layui/layui.js') }}" charset="utf-8"></script>
 
<script>
    layui.use('form', function(){
        var form = layui.form;

        form.verify({
            obtain_probability: function(value){
                if( $.trim(value).length < 1){
                    return '中奖概率不能为空';
                }
                var ex =  /^[0-9]+.?[0-9]*$/;
                if ( !ex.test(value) ) {
                   return '中奖概率必须为数字';
                }
                var num = parseFloat(value);
                if( num < 0 || num > 100 ){
                    return '中奖概率范围为0-100';
                }
            }
        });

        //监听提交
        form.on('submit(formDemo)', function(data){
            $.ajax({
                url:'{{ route("activity.prize.probability.update") }}',
                headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
                type:'put',
                data:$("#form-setting").serialize(),
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
            //layer.msg(JSON.stringify(data.field));
            //return false;
        });
    });
</script>
</html>