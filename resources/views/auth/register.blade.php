<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>注册 | AdminLTE 2</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/admin-lte/2.3.11/css/AdminLTE.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/iCheck/1.0.2/skins/square/blue.css">
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <a><b>Admin</b>LTE</a>
        </div>

        <div class="register-box-body">
            <p class="login-box-msg">注册新账号</p>

            <form action="{{ route('register') }}" method="post">
                {{ csrf_field() }}
                <div class="form-group has-feedback {{ $errors->has('name') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="name" placeholder="昵称" value="{{ old('name') }}">
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" class="form-control" name="email" placeholder="邮箱" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" class="form-control" name="password" placeholder="密码">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="再输入一次密码">
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <a href="{{ route('login') }}" class="text-center">已注册账号</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">注册</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="//cdn.bootcss.com/jquery/2.2.4/jquery.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/iCheck/1.0.2/icheck.min.js"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        });
    </script>
</body>
</html>