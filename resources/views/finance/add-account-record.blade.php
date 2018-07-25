<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <title>修改中奖概率</title>
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}"  media="all">
</head>
<body>
        <form id="form-setting" class="layui-form" onsubmit="return false" action="" style="margin-top: 20px">
            <div class="layui-form-item">
                <label class="layui-form-label">业务名称</label>
                <div class="layui-input-inline" style="width: 200px;">
                  <input type="text" name="business_alias" lay-verify="business_alias" placeholder="自定义名称，方便识别" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                  <button type="submit" class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">确定</button>
                </div>
            </div>
        </form>
</body>
<script src="{{ asset('js/jquery-1.8.3.min.js') }}" charset="utf-8"></script>
<script src="{{ asset('layui/layui.js') }}" charset="utf-8"></script>
 
<script>
    layui.use('form', function(){
        var form = layui.form;

        form.verify({
            business_alias: function(value){
                if( $.trim(value).length < 1){
                    return '业务名称不能为空';
                }
            }
        });

        //监听提交
        form.on('submit(formDemo)', function(data){
            $.ajax({
                url:'{{ route("finance.update-account-record") }}',
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