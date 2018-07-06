<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/jvectormap/1.2.2/jquery-jvectormap.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/admin-lte/2.3.11/css/AdminLTE.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/admin-lte/2.3.11/css/skins/_all-skins.min.css">
    <style type="text/css">
        .box-body-list{
            overflow-x: auto;
        }
    </style>
</head>
<body>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form id="form" method="POST" action="{{ route('user_store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label>上传附件</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button id="save" type="submit" class="btn btn-primary">确认上传</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>