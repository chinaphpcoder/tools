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
        <li><a href="">菜单管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <a href="{{url("menu/add")}}" class="btn btn-default" type="button">新增</a>
                    </div>
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center">ID</th>
                                    <th align="center">标题</th>
                                    <th align="center">排序值(s)</th>
                                    <th align="center">URL</th>
                                    <th align="center">提示</th>
                                    <th align="center">分组列表</th>
                                    <th align="center">状态</th>
                                    <th align="center">创建时间</th>
                                    <th align="center">更新时间</th>
                                    <th align="center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($list as $row)
                                <tr>
                                    <td align="center">{{$row->id}}</td>
                                    <td align="center">{{$row->title}}</td>
                                    <td align="center">{{$row->sort}}</td>
                                    <td align="center">{{$row->url}}</td>
                                    <td align="center">{{$row->tip}}</td>
                                    <td align="center">{{$row->group}}</td>
                                    <td align="center">{{$row->status}}</td>
                                    <td align="center">{{$row->created_at}}</td>
                                    <td align="center">{{$row->updated_at}}</td>
                                    <td align="center"><a class="btn btn-default" href="/menu/edit?id={{$row->id}}" role="button">编辑</a></td>
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