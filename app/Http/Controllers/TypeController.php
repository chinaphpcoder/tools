<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Models\ArticleType;
use Illuminate\Support\Facades\Auth;

/**
 * 文章类型
 */
class TypeController extends Controller{

    /**
     * 列表
     */
    public function index(Request $request){
        $type_id = $request->input('type');
        $list = ArticleType::orderBy('id', 'desc')->where('type_id', $type_id)->with('user')->paginate(20);

        $this->view_data['meta_title'] = '文章类型列表';
        $this->view_data['list'] = $list;
        $this->view_data['type_id'] = $type_id;
        return view('type.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(Request $request){
        $type_id = $request->input('type');
        $this->view_data['meta_title'] = '新增文章类型';
        $this->view_data['type_id'] = $type_id;
        return view('type.add', $this->view_data);
    }

    /**
     * 编辑
     */
    public function edit(Request $request){
        $type_id = $request->input('type');
        //获取ID
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        //查询指定记录
        $row = ArticleType::where('id', $id)->where('type_id', $type_id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑文章类型';
        $this->view_data['type_id'] = $type_id;
        return view('type.edit', $this->view_data);
    }

    /**
     * 新增|更新操作
     */
    public function update(Request $request){
        $type_id = $request->input('type');
        $id = $request->input('id', 0);

        $this->validate($request, [
            'title' => 'required|mb_max:20|mb_min:1',
        ]);

        if ($id < 1) { //新增
            $type = new ArticleType;
        } else {
            $type = ArticleType::where('id', $request->input('id'))->where('type_id', $type_id)->first();
        }

        $type->title = $request->input('title');
        $type->user_id = Auth::id();
        $type->type_id = $type_id;
        $result = $type->save();
        
        if ($result) {
            return $this->success(route('type_list', ['type' => $request->input('type')]));
        }else{
            return $this->error('操作失败');
        }
    }

    /**
     * 删除操作
     */
    public function delete(Request $request){
        $type_id = $request->input('type');
        $ids = $request->input('id');
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        $result = ArticleType::destroy($ids);
    
        if ($result) {
            return $this->success(route('type_list', ['type' => $request->input('type')]), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}