<?php

namespace App\Http\Controllers;

use App\Http\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * H5公告管理列表
     * @return [type] [description]
     */
    public function clientIndex()
    {
        $list = Notice::query()->where('type', '0')->orderBy('id', 'desc')->with('user')->paginate(20);
        $display_status = ['未发布', '已发布'];
        $push_status = ['未推送', '已推送'];
        foreach ($list as &$value) {
            $value->display_text = isset($display_status) ? $display_status[$value->display] : '未知';
            $value->push_text = isset($push_status) ? $push_status[$value->push_status] : '未知';
        }

        $this->view_data['meta_title'] = 'APP公告列表';
        $this->view_data['list'] = $list;
        return view('notice.index', $this->view_data);
    }

    public function clientAdd()
    {
        $this->view_data['meta_title'] = 'APP公告';
        return view('notice.add', $this->view_data);
    }

    public function clientUpdate(Request $request)
    {
        $id = $request->input('id', 0);
        $this->validate($request, [
            'title' => 'required|mb_max:40|mb_min:1',
            'started_at' => 'required|date',
            'ended_at' => 'required|date',
            'content' => 'required|min:1'
        ]);

        if ($id < 1) {
            $article = new Notice();
        } else {
            $article = Notice::query()->where('id', $request->input('id'))->first();
        }

        $status = intval($request->input('status', 0));

        $article->title = $request->input('title');
        $article->content = $request->input('content');
        $article->started_at = $request->input('started_at');
        $article->ended_at = $request->input('ended_at');
        $article->display = $status;
        $article->user_id = Auth::id();
        $article->push_ip = $request->getClientIp();

        if ($status == 1) {
            $article->published_at = date('Y-m-d H:i:s');
        }

        $result = $article->save();

        if ($result) {
            return $this->success(route('app_notice'));
        } else {
            return $this->error('操作失败');
        }
    }

    public function clientEdit(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        //查询指定记录
        $row = Notice::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑APP公告';
        return view('notice.edit', $this->view_data);
    }

    public function clientDelete(Request $request)
    {
        $ids = $request->input('id');
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        $result = Notice::destroy($ids);

        if ($result) {
            return $this->success(route('app_notice'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function clientStatus(Request $request)
    {
        $id = $request->input('id', 0);
        $id = intval($id);
        if ($id < 1) {
            return $this->error('请选择需要操作的记录');
        }

        //获取记录
        $row = Notice::find($id);
        if (empty($row)) {
            return $this->error('暂未查询到相关记录');
        }

        $display = $row->display;

        //判断
        if ($display == 1) { //下线
            $row->display = 0;
            $row->published_at = null;
        } else { //上线
            $row->display = 1;
            $row->published_at = date('Y-m-d H:i:s');
        }

        if ($row->save()) {
            return $this->success(route('app_notice'));
        } else {
            return $this->success('操作失败');
        }
    }
}