@extends('public.base')

@section('css')
    <link rel="stylesheet" href="//cdn.bootcss.com/datatables/1.10.15/css/dataTables.bootstrap.min.css">
    <style type="text/css">
        th{
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>
            {{$meta_title}}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
            <li><a href="javascript:;">欢乐大转盘</a></li>
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <a href="javascript:;" class="btn btn-default" onclick="addExcel()" type="button">上传EXCEL文件</a>
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th align="center">ID</th>
                                    <th align="center">文件名称</th>
                                    <th align="center">上传时间</th>
                                    <th align="center">发布人</th>
                                    <th align="center">操作</th>
                                </tr>
                                </thead>
                                <tbody id="list">
                                @foreach ($list as $row)
                                    <tr>
                                        <td align="center">{{ $row->id }}</td>
                                        <td align="center">{{ $row->original_filename }}</td>
                                        <td align="center">{{ $row->upload_time }}</td>
                                        <td align="center">{{ $row->admin_name }}</td>
                                        <td align="center">
                                            <a href="javascript:;" class="btn btn-danger" onclick="delConfirm('{{ route('user_delete', ['id' => $row->id]) }}')">删除</a>
                                            <a href="{{ route('user_download', ['id' => $row->id]) }}" class="btn btn-success">下载</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ url('js/layer/layer.js') }}"></script>
    <script type="text/javascript">
        // 弹出添加Excel框
        function addExcel() {
            layer.open({
                type: 2,
                title: '上传Excel',
                area: ['500px', '300px'],
                shade: [0],
                content: '{{ route('user_create') }}'
            })
        }
        function delConfirm(url) {
            layer.confirm('确认删除吗？', {
            }, function (index) {
                window.location.href = url;
            });
        }
    </script>

@endsection