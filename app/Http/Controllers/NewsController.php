<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Models\News;
use App\Http\Models\ArticleType;
use App\Http\Models\ArticleSource;
use Illuminate\Support\Facades\Auth;


/**
 * 小沙学院
 */
class NewsController extends Controller{

    /**
     * 列表
     */
    public function index(){
        $list = News::orderBy('id', 'desc')->with('user', 'type', 'source')->paginate(20);

        $status_status = ['未发布', '已发布', '已下线'];

        foreach ($list as &$value) {
            $value->status_text = isset($status_status[$value->status]) ? $status_status[$value->status] : '未知';
        }

        $this->view_data['meta_title'] = '媒体报道列表';
        $this->view_data['list'] = $list;
        return view('news.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        $type = ArticleType::where('type_id', 2)->select('id', 'title')->get();
        $source = ArticleSource::all();
        $this->view_data['source'] = $source;
        $this->view_data['type'] = $type;
        $this->view_data['meta_title'] = '新增媒体报道';
        return view('news.add', $this->view_data);
    }

    /**
     * 编辑
     */
    public function edit(Request $request){
        //获取ID
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        //文章类型
        $type = ArticleType::where('type_id', 2)->select('id', 'title')->get();
        $this->view_data['type'] = $type;

        //文章来源
        $source = ArticleSource::all();
        $this->view_data['source'] = $source;
        
        //查询指定记录
        $row = News::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }
        
        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑媒体报道';
        return view('news.edit', $this->view_data);
    }

    /**
     * 新增|更新操作
     */
    public function update(Request $request){
        $id = $request->input('id', 0);

        $status = intval($request->input('status', 0));

        $this->validate($request, [
            'title' => 'required|mb_max:40|mb_min:1',
            'type_id' => 'required|integer|min:1',
            'content' => 'required|min:1',
            'source_id' => 'required|integer|min:1',
        ]);

        //判断文章类型
        if ($request->input('type_id') < 1) {
            return $this->error('请选择文章分类');
        }
        //判断文章来源
        if ($request->input('type_id') < 1) {
            return $this->error('请选择文章来源');
        }

        if ($id < 1) { //新增
            $news = new News;
        } else {
            $news = News::find($request->input('id'));
        }

        $news->title = $request->input('title');
        $news->content = $request->input('content');
        $news->type_id = $request->input('type_id');
        $news->source_id = $request->input('source_id');
        $news->status = $status;
        $news->user_id = Auth::id();

        if ($status == 1) {
            $news->published_at = date('Y-m-d H:i:s');
        }

        $result = $news->save();
        
        if ($result) {
            return $this->success(route('news_list'));
        }else{
            return $this->error('操作失败');
        }
    }

    /**
     * 修改状态
     */
    public function status(Request $request){
        $id = $request->input('id', 0);
        $id = intval($id);
        if ($id < 1) {
            return $this->error('请选择需要操作的记录');
        }

        //获取记录
        $row = News::find($id);
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
            return $this->success(route('news_list'));
        } else {
            return $this->success('操作失败');
        }
    }

    /**
     * 删除操作
     */
    public function delete(Request $request){
        $ids = $request->input('id');
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        $result = News::destroy($ids);
    
        if ($result) {
            return $this->success(route('news_list'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}