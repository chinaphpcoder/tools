<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$meta_title}} | AdminLTE 2</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{ asset('bootstrap-3.3.7/css/bootstrap.min.css') }}">
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
    @yield('css')
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <a class="logo">
                <span class="logo-mini"><b>A</b>LT</span>
                <span class="logo-lg"><b>Admin</b>LTE</span>
            </a>
            <nav class="navbar navbar-static-top">
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="//cdn.bootcss.com/admin-lte/2.3.11/img/user2-160x160.jpg" class="user-image" alt="User Image">
                                <span class="hidden-xs">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <img src="//cdn.bootcss.com/admin-lte/2.3.11/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                    <p>
                                        {{ Auth::user()->name }} - Web 开发者
                                        <small>加入时间 {{Auth::user()->created_at }}</small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-default btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">退出</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="//cdn.bootcss.com/admin-lte/2.3.11/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ Auth::user()->name }}</p>
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>
                <ul class="sidebar-menu">
                    @foreach($admin_menu as $key => $row)
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-dashboard"></i> <span>{{$key}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @foreach($row as $info)
                            <li><a href="{{url($info->url)}}" title="{{$info->tip}}"><i class="fa fa-circle-o"></i>{{$info->title}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>版本</b> 0.0.1
            </div>
            <strong>Copyright &copy; 2017-{{date('Y')}} <a target="_blank" href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
            reserved.
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
            <ul class="nav nav-tabs nav-justified control-sidebar-tabs"></ul>
            <div class="tab-content">
                <div class="tab-pane" id="control-sidebar-home-tab"></div>
            </div>
        </aside>
        <div class="control-sidebar-bg"></div>
    </div>
    <script src="{{ asset('js/jquery-2.2.4.min.js') }}"></script>
    <script src="{{ asset('bootstrap-3.3.7/js/bootstrap.min.js') }}"></script>
    <script src="//cdn.bootcss.com/fastclick/1.0.6/fastclick.min.js"></script>
    <script src="//cdn.bootcss.com/admin-lte/2.3.11/js/app.min.js"></script>
    <script src="//cdn.bootcss.com/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
    <script src="//cdn.bootcss.com/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
    <script src="//cdn.bootcss.com/admin-lte/2.3.8/js/demo.js"></script>
    @yield('javascript')
    <script type="text/javascript">
        $(function(){
            var url = window.location.href;

            //左侧菜单展开
            $('.sidebar-menu').find('.treeview-menu').find('a').each(function(value){
                if ($(this).attr('href') == window.location.href) {
                    $(this).parent().addClass('active');
                    $(this).parent().parent().parent().addClass('active');
                    $(this).parent().parent().show();
                }
            });
        })
    </script>
</body>
</html>
