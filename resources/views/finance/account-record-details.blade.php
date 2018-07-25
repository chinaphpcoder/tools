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
                <td>上传选项</td>
                <td>
                    <div class="class="layui-form"">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="width: 60px">包含表头</label>
                                <div class="layui-input-inline" style="width: 30px">
                                    <input type="checkbox" checked="" name="upload-basic-header" lay-skin="switch" lay-filter="switchTest" lay-text="是|否">
                                </div>
                                <label class="layui-form-label" style="width: 60px">流水号列</label>
                                <div class="layui-input-inline" style="width: 40px;">
                                    <input type="text" name="upload-basic-request-no" placeholder="" autocomplete="off" class="layui-input" value="A">
                                </div>
                                <label class="layui-form-label" style="width: 50px">金额列</label>
                                <div class="layui-input-inline" style="width: 40px;">
                                    <input type="text" name="upload-basic-amount" placeholder="" autocomplete="off" class="layui-input"  value="B">
                                </div>
                                <label class="layui-form-label" style="width: 70px">过滤字符串</label>
                                <div class="layui-input-inline" style="width: 50px;">
                                    <input type="text" name="upload-basic-trim" placeholder="" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
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
                <td>上传选项</td>
                <td>
                    <div class="class="layui-form"">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="width: 60px">包含表头</label>
                                <div class="layui-input-inline" style="width: 30px">
                                    <input type="checkbox" checked="" name="upload-actual-header" lay-skin="switch" lay-filter="switchTest" lay-text="是|否">
                                </div>
                                <label class="layui-form-label" style="width: 60px">流水号列</label>
                                <div class="layui-input-inline" style="width: 40px;">
                                    <input type="text" name="upload-actual-request-no" placeholder="" autocomplete="off" class="layui-input" value="A">
                                </div>
                                <label class="layui-form-label" style="width: 50px">金额列</label>
                                <div class="layui-input-inline" style="width: 40px;">
                                    <input type="text" name="upload-actual-amount" placeholder="" autocomplete="off" class="layui-input"  value="B">
                                </div>
                                <label class="layui-form-label" style="width: 70px">过滤字符串</label>
                                <div class="layui-input-inline" style="width: 50px;">
                                    <input type="text" name="upload-actual-trim" placeholder="" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
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
        var uploadBasic = upload.render({
            elem: '#upload-basic-select'
            ,url: '{{ route("finance.upload-basic-data") }}?business_identity_id={{ $business_identity_id }}'
            ,headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
            ,data:{header: '',column_request_no: '',column_amount: '',trim_string: ''}
            ,auto: false
            ,accept:'file'
            //,multiple: true
            ,bindAction: '#upload-basic-submit'
            ,before: function(obj){ 
                uploadBasic.config.data.header = $("[name='upload-basic-header']").prop("checked") ? '1' : '0';
                uploadBasic.config.data.column_request_no = $("[name='upload-basic-request-no']").val();
                uploadBasic.config.data.column_amount = $("[name='upload-basic-amount']").val();
                uploadBasic.config.data.trim_string = $("[name='upload-basic-trim']").val();
            }
            ,done: function(res){
                console.log(res);
                layer.msg(res.msg
                        ,{time:500}
                        ,function(){
                            location.reload();
                        });
            }
        });

        //选完文件后不自动上传
        upload.render({
            elem: '#upload-actual-select'
            ,url: '{{ route("finance.upload-actual-data") }}?business_identity_id={{ $business_identity_id }}'
            ,headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
            ,data:{header: '',column_request_no: '',column_amount: '',trim_string: ''}
            ,auto: false
            ,accept:'file'
            //,multiple: true
            ,bindAction: '#upload-actual-submit'
            ,before: function(obj){ 
                uploadBasic.config.data.header = $("[name='upload-actual-header']").prop("checked") ? '1' : '0';
                uploadBasic.config.data.column_request_no = $("[name='upload-actual-request-no']").val();
                uploadBasic.config.data.column_amount = $("[name='upload-actual-amount']").val();
                uploadBasic.config.data.trim_string = $("[name='upload-actual-trim']").val();
            }
            ,done: function(res){
                console.log(res);
                layer.msg(res.msg
                        ,{time:500}
                        ,function(){
                            location.reload();
                        });
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