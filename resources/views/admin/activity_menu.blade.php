@extends('public.base')

@section('css')
<link rel="stylesheet" href="//cdn.bootcss.com/datatables/1.10.15/css/dataTables.bootstrap.min.css">
@endsection

@section('content')
<section class="content-header">
    <h1>
        {{$meta_title}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>首页</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <a href="{{route('admin.activity_add')}}" class="btn btn-default" type="button">新增</a>
                    </div>
                    <div class="box-body box-body-list">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">活动名称</th>
                                    <th class="text-center">创建时间</th>
                                    <th class="text-center">操作</th>
                                </tr>
                            </thead>
                            <tbody id="list">
                            @foreach ($list as $row)
                                <tr>
                                    <td align="center"><input type="checkbox" value="{{$row->id}}"></td>
                                    <td align="center">{{$row->group}}</td>
                                    <td align="center">{{$row->created_at}}</td>
                                    <td align="center">
                                        <a class="btn btn-default row-delete" href="{{route('admin.activity_delete', ['group' => $row->group])}}" role="button">删除</a>
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
<script type="text/javascript">
    //单个删除
    $('.row-delete').click(function(){
        //删除确认
        var status = confirm('您确定要删除吗？');
        if (!status) {
            return false;
        }
    })
</script>

@endsection