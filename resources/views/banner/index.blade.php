@extends('public.base')

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
                            <input id="all" type="checkbox"> 全选
                            <a href="{{route('banner_add')}}" class="btn btn-default" type="button">新增</a>
                            <a href="{{route('banner_delete')}}" id="deleteBulk" class="btn btn-default" type="button">批量删除</a>
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">轮播图排序设置</button>
                            <a href="{{route('banner_index')}}" id="deleteBulk" class="btn btn-default <?=(is_null($status) ? 'active' : '')?>" type="button">全部</a>
                            <a href="{{route('banner_index', ['status' => 0])}}" id="deleteBulk" class="btn btn-default <?=($status === 0 ? 'active' : '')?>" type="button">待发布</a>
                            <a href="{{route('banner_index', ['status' => 1])}}" id="deleteBulk" class="btn btn-default <?=($status === 1 ? 'active' : '')?>" type="button">已上线</a>
                            <a href="{{route('banner_index', ['status' => 2])}}" id="deleteBulk" class="btn btn-default <?=($status === 2 ? 'active' : '')?>" type="button">已下线</a>
                        </div>
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">排序设置</h4>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            @foreach($online_list as $key => $row)
                                                <li class="list-group-item"><span data-id="{{$key}}" class="badge move_up">上</span><span data-id="{{$key}}" class="badge move_down">下</span>{{$key + 1}}. ID【<span class="order_ids id_{{$key}}">{{$row->id}}</span>】 标题【<span class="title_{{$key}}">{{$row->title}}</span>】</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                        <button id="enter_order" type="button" class="btn btn-primary">确定</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body box-body-list">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th align="center"></th>
                                    <th align="center">ID</th>
                                    <th align="center">缩略图</th>
                                    <th align="center">轮播图名称</th>
                                    <th align="center">状态</th>
                                    <th align="center">发布人</th>
                                    <th align="center">开始时间</th>
                                    <th align="center">结束时间</th>
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
                                        <td align="center"><img width="150" src="{{$row->image_url}}"></td>
                                        <td align="center">{{$row->title}}</td>
                                        <td align="center">{{$row->status == 1 ? '显示' : '关闭'}}</td>
                                        <td align="center">{{isset($row->user) ? $row->user->name : '未知'}}</td>
                                        <td align="center">{{$row->started_at}}</td>
                                        <td align="center">{{$row->ended_at}}</td>
                                        <td align="center">{{$row->created_at}}</td>
                                        <td align="center">{{$row->updated_at}}</td>
                                        <td align="center">
                                            @if($row->status == 0)
                                                <a class="btn btn-default" href="{{route('banner_status', ['id' => $row->id, 'status' => $status])}}" role="button">发布</a>
                                            @elseif($row->status == 2)
                                                <a class="btn btn-default" href="{{route('banner_status', ['id' => $row->id, 'status' => $status])}}" role="button">上线</a>
                                            @else
                                                <a class="btn btn-default" href="{{route('banner_status', ['id' => $row->id, 'status' => $status])}}" role="button">下线</a>
                                            @endif
                                            <a class="btn btn-default" href="{{route('banner_edit', ['id' => $row->id])}}" role="button">编辑</a>
                                            <a class="btn btn-default row-delete" href="{{route('banner_delete', ['id' => $row->id])}}" role="button">删除</a>
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

            //排序 往上
            $('.move_up').click(function(){
                var num = $(this).attr('data-id');
                num = parseInt(num);
                //检测是否到了顶部
                if (num == 0) {
                    alert('已经到了顶部了');
                    return false;
                }

                //获取上一条ID
                var prev_id = $('.id_' + (num -1)).text();
                var prev_title = $('.title_' + (num -1)).text();

                //获取当前ID
                var current_id = $('.id_' + num).text();
                var current_title = $('.title_' + num).text();

                //上一条记录
                $('.id_' + (num - 1)).text(current_id);
                $('.title_' + (num - 1)).text(current_title);

                //上条记录移到本条记录
                $('.id_' + (num)).text(prev_id);
                $('.title_' + (num)).text(prev_title);
            })

            //排序 往下
            $('.move_down').click(function(){
                var num = $(this).attr('data-id');
                num = parseInt(num);
                //检测是否到了顶部

                if ($('.id_' + (num +1)).length == 0) {
                    alert('已经到了底部了');
                    return false;
                }

                //获取当前ID
                var current_id = $('.id_' + num).text();
                var current_title = $('.title_' + num).text();

                //获取下一条ID
                var next_id = $('.id_' + (num + 1)).text();
                var next_title = $('.title_' + (num + 1)).text();

                //上一条记录
                $('.id_' + (num + 1)).text(current_id);
                $('.title_' + (num + 1)).text(current_title);

                //上条记录移到本条记录
                $('.id_' + (num)).text(next_id);
                $('.title_' + (num)).text(next_title);
            })

            //确定排序
            $('#enter_order').click(function(){
                var valArr = new Array;

                $(".order_ids").each(function(i) {
                    valArr[i] = $(this).text();
                });
                var str = valArr.join(',');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.post('{{route("banner_order")}}', {'ids': str}, function(data){
                    alert(data.info);
                    if (data.status) {
                        window.location.reload();
                    }
                }, 'json');
            })
        });
    </script>

@endsection