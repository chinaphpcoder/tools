<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <title>修改中奖概率</title>
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}"  media="all">
</head>
<body>
    <div style="width: 300px">
        <form id="form-setting" class="layui-form" onsubmit="return false" action="" style="margin-top: 20px">
            <div class="layui-form-item">
                <label class="layui-form-label">中奖概率</label>
                <div class="layui-input-inline" style="width: 200px;">
                  <input type="text" name="obtain_probability" lay-verify="obtain_probability" placeholder="范围0-100，最多两位小数" class="layui-input">
                </div>
                <input type="hidden" name="id" value="{{ $id }}">
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                  <button type="submit" class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">确定</button>
                </div>
            </div>
        </form>
    </div>
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
                url:'{{ route("probability_update") }}',
                headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
                type:'post',
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