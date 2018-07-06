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
        <li><a href="{{route('college_list')}}">H5管理</a></li>
        <li class="active">{{$meta_title}}</li>
    </ol>
</section>
<section class="content">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <input id="all" type="checkbox"> 全选 
                        <a href="{{route('article_add')}}" class="btn btn-default" type="button">新增</a>
                        <a href="{{route('article_delete')}}" id="deleteBulk" class="btn btn-default" type="button">批量删除</a>
                    </div>
                    <div class="box-body box-body-list">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center"></th>
                                    <th align="center">ID</th>
                                    <th align="center">文章标题</th>
                                    <th align="center">文章分类</th>
                                    <th align="center">状态</th>
                                    <th align="center">浏览次数</th>
                                    <th align="center">点赞次数</th>
                                    <th align="center">发布人</th>
                                    <th align="center">发布时间</th>
                                    <th align="center">创建时间</th>
                                    <th align="center">更新时间</th>
                                    <th align="center">操作</th>
                                </tr>
                            </thead>
                            <tbody id="list">
                            @foreach ($list as $row)
                                <tr>
                                    <td align="center"><input type="checkbox" value="{{$row->id}}"></td>
                                    <td align="center">{{$row->id}}</td>
                                    <td align="center">{{$row->title}}</td>
                                    <td align="center">{{isset($row->type) ? $row->type->title : ''}}</td>
                                    <td align="center">{{$row->status_text}}</td>
                                    <td align="center">{{$row->view_count}}</td>
                                    <td align="center">{{$row->zan_count}}</td>
                                    <td align="center">{{isset($row->user) ? $row->user->name : '未知'}}</td>
                                    <td align="center">{{$row->published_at}}</td>
                                    <td align="center">{{$row->created_at}}</td>
                                    <td align="center">{{$row->updated_at}}</td>
                                    <td align="center">
                                        @if($row->status == 1)
                                            <a class="btn btn-default" href="{{route('article_status')}}?id={{$row->id}}" role="button">下线</a>
                                        @elseif($row->status == 2)
                                            <a class="btn btn-default" href="{{route('article_status')}}?id={{$row->id}}" role="button">上线</a>
                                        @endif
                                        <a class="btn btn-default" href="{{route('article_edit')}}?id={{$row->id}}" role="button">编辑</a>
                                        <a class="btn btn-default row-delete" href="{{route('article_delete')}}?id={{$row->id}}" role="button">删除</a>
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
                <div class="dataTables_info" role="status" aria-live="polite">总数：{{ $list->total()}}</div>
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
    $(function() {
        //全选或全不选
        $("#all").click(function() {
            if (this.checked) {
                $("#list :checkbox").prop("checked", true);
            } else {
                $("#list :checkbox").prop("checked", false);
            }
        });
    });

    //单个删除
    $('.row-delete').click(function(){
        //删除确认
        var status = confirm('您确定要删除吗？');
        if (!status) {
            return false;
        }
    })

    //批量删除
    $('#deleteBulk').click(function(){
        var valArr = new Array;
        $("#list").find('input:checkbox:checked').each(function(i) {
            valArr[i] = $(this).val();
        });
        var str = valArr.join(',');

        if (str.length == 0) {
            alert('请选择要删除的记录');
            return false;
        }

        //删除确认
        var status = confirm('您确定要删除吗？');
        if (!status) {
            return false;
        }

        window.location= $(this).attr('href') + '?id=' + str;
        return false;
    })
</script>

@endsection