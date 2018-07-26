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
        <li><a href="{{route('user.index')}}">后台用户</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <a href="{{route('user_add')}}" class="btn btn-default" type="button">新增</a>
                    </div>
                    <div class="box-body box-body-list">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">名称</th>
                                    <th class="text-center">邮箱</th>
                                    <th class="text-center">创建时间</th>
                                    <th class="text-center">更新时间</th>
                                    <th class="text-center">操作</th>
                                </tr>
                            </thead>
                            <tbody id="list">
                            @foreach ($list as $row)
                                <tr>
                                    <td align="center"><input type="checkbox" value="{{$row->id}}"></td>
                                    <td align="center">{{$row->id}}</td>
                                    <td align="center">{{$row->name}}</td>
                                    <td align="center">{{$row->email}}</td>
                                    <td align="center">{{$row->created_at}}</td>
                                    <td align="center">{{$row->updated_at}}</td>
                                    <td align="center">
                                        <a class="btn btn-default" href="{{route('user_edit', ['id' => $row->id])}}" role="button">编辑</a>
                                        <a class="btn btn-default row-delete" href="{{route('user_delete', ['id' => $row->id])}}" role="button">删除</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">总数：{{ $list->total()}}</div>
            </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $list->links() }}
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