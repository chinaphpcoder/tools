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
        <legend>总体概况</legend>
    </fieldset>
    <div class="layui-form">
        <table class="layui-table">
            <colgroup>
                <col width="100">
                <col width="200">
            </colgroup>
            <tbody>
            @foreach($overall_data as $key=>$value)
            <tr>
                <td>{{ $value['key'] }}</td>
                <td>{{ $value['value'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>基准数据</legend>
    </fieldset>
    <div class="layui-form">
        <table class="layui-table">
            <colgroup>
                <col width="100">
                <col width="200">
            </colgroup>
            <tbody>
            <tr>
                <td>数据上传</td>
                <td>
                    <div class="layui-upload">
                        <button type="button" class="layui-btn layui-btn-sm" id="upload-basic-select">选择文件</button>
                        <button type="button" class="layui-btn layui-btn-sm" id="upload-basic-submit">开始上传</button>
                    </div>
                </td>
            </tr>
            @foreach($basic_data as $key=>$value)
            <tr>
                <td>{{ $value['key'] }}</td>
                <td>{{ $value['value'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>实际数据</legend>
    </fieldset>
    <div class="layui-form">
        <table class="layui-table">
            <colgroup>
                <col width="100">
                <col width="200">
            </colgroup>
            <tbody>
            <tr>
                <td>数据上传</td>
                <td>
                    <div class="layui-upload">
                        <button type="button" class="layui-btn layui-btn-sm" id="upload-actual-select">选择文件</button>
                        <button type="button" class="layui-btn layui-btn-sm" id="upload-actual-submit">开始上传</button>
                    </div>
                </td>
            </tr>
            @foreach($actual_data as $key=>$value)
            <tr>
                <td>{{ $value['key'] }}</td>
                <td>{{ $value['value'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</body>
<script src="{{ asset('js/jquery-1.8.3.min.js') }}" charset="utf-8"></script>
<script src="{{ asset('layui/layui.js') }}" charset="utf-8"></script>
 
<script>
    layui.use(['form','upload'], function(){
        var form = layui.form
            ,upload = layui.upload;

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

        //选完文件后不自动上传
        upload.render({
            elem: '#upload-basic-select'
            ,url: '{{ route("finance.upload-basic-data") }}'
            ,headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
            ,auto: false
            ,accept:'file'
            //,multiple: true
            ,bindAction: '#upload-basic-submit'
            ,done: function(res){
                console.log(res)
            }
        });

        //选完文件后不自动上传
        upload.render({
            elem: '#upload-actual-select'
            ,url: '{{ route("finance.upload-actual-data") }}'
            ,headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
            ,auto: false
            ,accept:'file'
            //,multiple: true
            ,bindAction: '#upload-actual-submit'
            ,done: function(res){
                console.log(res)
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