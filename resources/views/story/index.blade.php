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
            <li><a href="{{route('story_index', ['type' => $type])}}">小沙故事</a></li>
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
                            <a href="{{route('story_add', ['type' => $type])}}" class="btn btn-default" type="button">新增</a>
                            <a href="{{route('story_delete', ['type' => $type])}}" id="deleteBulk" class="btn btn-default" type="button">批量删除</a>
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th align="center"></th>
                                    <th align="center">ID</th>
                                    <th align="center">文章标题</th>
                                    <th align="center">文章分类</th>
                                    <th align="center">评论数</th>
                                    <th align="center">浏览次数</th>
                                    <th align="center">点赞</th>
                                    <th align="center">发布人</th>
                                    <th align="center">发布时间</th>
                                    <th align="center">标签</th>
                                    <th align="center">操作</th>
                                </tr>
                                </thead>
                                <tbody id="list">
                                    @foreach($lists as $row)
                                        <tr>
                                            <td align="center"><input type="checkbox" name="id" value="{{$row->id}}"></td>
                                            <td align="center">{{ $row->id }}</td>
                                            <td align="center">{{ $row->title }}</td>
                                            <td align="center">{{ $meta_title }}</td>
                                            <td align="center">
                                                {{ !empty($row->comments()->where('comment_id', 0)->count()) ? $row->comments()->where('comment_id', 0)->count() : 0 }}
                                                </td>
                                            <td align="center">{{ $row->views }}</td>
                                            <td align="center">{{ $row->thumb_up }}</td>
                                            <td align="center">{{isset($row->user) ? $row->user->name : '未知'}}</td>
                                            <td align="center">{{ $row->published_at }}</td>
                                            <td align="center">
                                                @if($row->attr == 1)
                                                    置顶
                                                @elseif($row->attr == 2)
                                                    精品
                                                @endif

                                                @if($row->status == 2)
                                                   &nbsp;&nbsp; 隐藏
                                                @elseif($row->status == 3)
                                                        &nbsp;&nbsp; 暂存
                                                @endif
                                            </td>
                                            <td align="center">
                                                @if($row->status == 2)
                                                    <a onclick="javascript:return showConfirm(this, '您确认将当前数据取消隐藏吗？')" href="{{ route('story_status', ['id' => $row->id, 'type' => $type, 'status' => 1]) }}">取消隐藏</a>&nbsp;&nbsp;
                                                @elseif($row->status == 3)
                                                    <span style="color: #ccc;">隐藏</span>&nbsp;&nbsp;
                                                @else
                                                    <a onclick="javascript:return showConfirm(this, '您确认将当前数据隐藏吗？')" href="{{ route('story_status', ['id' => $row->id, 'type' => $type, 'status' => 2]) }}">隐藏</a>&nbsp;&nbsp;
                                                @endif

                                                <a href="{{ route('story_edit', ['id' => $row->id, 'type' => $type]) }}">编辑</a>&nbsp;&nbsp;
                                                <a class="row-delete" href="{{route('story_delete', ['id' => $row->id, 'type' => $type]) }}">删除</a>&nbsp;&nbsp;

                                                @if($row->attr == 1)
                                                    <a onclick="javascript:return showConfirm(this, '您确认将当前数据取消置顶吗？')" href="{{ route('story_status', ['id' => $row->id, 'type' => $type, 'status' => 3]) }}">取消置顶</a>&nbsp;&nbsp;
                                                    <span style="color: #ccc;">精品</span>&nbsp;&nbsp;
                                                @elseif($row->attr == 2)
                                                    <span style="color: #ccc;">置顶</span>&nbsp;&nbsp;
                                                    <a onclick="javascript:return showConfirm(this, '您确认将当前数据取消精品吗？')" href="{{ route('story_status', ['id' => $row->id, 'type' => $type, 'status' => 4]) }}">取消精品</a>
                                                @else
                                                        <a onclick="javascript:return showConfirm(this, '您确认将当前数据设为置顶吗？')" href="{{ route('story_status', ['id' => $row->id, 'type' => $type, 'status' => 5]) }}">置顶</a>&nbsp;&nbsp;
                                                        <a onclick="javascript:return showConfirm(this, '您确认将当前数据设为精品吗？')" href="{{ route('story_status', ['id' => $row->id, 'type' => $type, 'status' => 6]) }}">精品</a>&nbsp;&nbsp;
                                                @endif
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
                    <div class="dataTables_info" role="status" aria-live="polite">总数：{{ $lists->total()}}</div>
                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $lists->links() }}
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

            window.location= $(this).attr('href') + '&id=' + str;
            return false;
        });

        function showConfirm(obj, tips) {
            if (confirm(tips) == true) {
                return true;
            } else {
                return false;
            }
        }
    </script>

@endsection