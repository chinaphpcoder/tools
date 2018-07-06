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
            <li><a href="{{route('friend_list')}}">合作机构</a></li>
            <li class="active">{{$meta_title}}</li>
        </ol>
    </section>
    <section class="content">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <input id="all" type="checkbox" name="ids[]" value="">全选
                            <a href="{{url('friend/add')}}" class="btn btn-primary" type="button">新增</a>
                            <a id="deleteBulk" href="{{route('friend_delete')}}" type="button" class="btn btn-danger">批量删除</a>
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">排序</th>
                                    <th class="text-center">缩略图</th>
                                    <th class="text-center">名称</th>
                                    <th class="text-center">发布人</th>
                                    <th class="text-center">发布时间</th>
                                    <th class="text-center">更新时间</th>
                                    <th class="text-center">操作</th>
                                </tr>
                                </thead>
                                <tbody id="list">
                                @foreach ($list as $row)
                                    <tr>
                                        <td class="text-center"><input type="checkbox" value="{{$row->id}}"></td>
                                        <td class="text-center">{{$row->sort}}</td>
                                        <td class="text-center"><img width="150" src="{{$row->m_pic}}"></td>
                                        <td class="text-center">{{$row->title}}</td>
                                        <td class="text-center">{{isset($row->user) ? $row->user->name : '未知'}}</td>
                                        <td class="text-center">{{$row->created_at}}</td>
                                        <td class="text-center">{{$row->updated_at}}</td>
                                        <td class="text-center">
                                            <a class="btn btn-warning" href="{{route('friend_edit', ['id' => $row->id])}}" role="button">编辑</a>
                                            <a class="btn btn-danger row-delete" href="{{route('friend_delete', ['id' => $row->id])}}" role="button">删除</a>
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