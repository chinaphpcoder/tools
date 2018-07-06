<?php

namespace App\Http\Controllers;

use App\Http\Models\Article;
use App\Http\Models\ArticleType;
use Illuminate\Http\Request;
use Auth;

class ArticleController extends Controller
{
    /**
     * H5公告管理列表
     * @return [type] [description]
     */
    public function index()
    {
        $list = Article::query()->orderBy('id', 'desc')->with('user', 'type')->paginate(20);
        $status_status = ['未发布', '已发布', '已下线'];
        foreach ($list as &$value) {
            $value->status_text = isset($status_status) ? $status_status[$value->status] : '未知';
        }

        $this->view_data['meta_title'] = 'H5公告列表';
        $this->view_data['list'] = $list;
        return view('article.index', $this->view_data);
    }

    public function add()
    {
        $type = ArticleType::query()->where('type_id', 3)->select(['id', 'title'])->get();
        $this->view_data['type'] = $type;
        $this->view_data['meta_title'] = '新H5公告';
        return view('article.add', $this->view_data);
    }

    public function update(Request $request)
    {
        $id = $request->input('id', 0);
        $this->validate($request, [
            'title' => 'required|mb_max:40|mb_min:1',
            'type_id' => 'required|integer|min:1',
            'content' => 'required|min:1'
        ]);

        if ($id < 1) {
            $article = new Article();
        } else {
            $article = Article::query()->where('id', $request->input('id'))->first();
        }

        $status = intval($request->input('status', 0));

        $article->title = $request->input('title');
        $article->content = $request->input('content');
        $article->type_id = $request->input('type_id');
        $article->status = $status;
        $article->user_id = Auth::id();

        if ($status == 1) {
            $article->published_at = date('Y-m-d H:i:s');
        }

        $result = $article->save();

        if ($result) {
            return $this->success(route('article_index'));
        } else {
            return $this->error('操作失败');
        }
    }

    public function edit(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        $type = ArticleType::where('type_id', 3)->select('id', 'title')->get();
        $this->view_data['type'] = $type;
        
        //查询指定记录
        $row = Article::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }
        
        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑H5公告';
        return view('article.edit', $this->view_data);
    }

    public function delete(Request $request)
    {
        $ids = $request->input('id');
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        $result = Article::destroy($ids);
    
        if ($result) {
            return $this->success(route('article_index'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function status(Request $request)
    {
        $id = $request->input('id', 0);
        $id = intval($id);
        if ($id < 1) {
            return $this->error('请选择需要操作的记录');
        }

        //获取记录
        $row = Article::find($id);
        if (empty($row)) {
            return $this->error('暂未查询到相关记录');
        }

        $status = $row->status;

        //判断是否为未发布状态
        if ($status == 0) {
            return $this->error('请在编辑中进行操作');
        }

        //判断
        if ($status == 1) { //下线
            $row->status = 2;
        } else { //上线
            $row->status = 1;
            $row->published_at = date('Y-m-d H:i:s');
        }

        if ($row->save()) {
            return $this->success(route('article_index'));
        } else {
            return $this->success('操作失败');
        }
    }
}   