<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Models\Legal;
use Illuminate\Support\Facades\Auth;

/**
 * 法律法规
 */
class LegalController extends Controller{

    /**
     * 列表
     */
    public function index(){
        $list = Legal::orderBy('id', 'desc')->with('user')->paginate(20);

        $this->view_data['meta_title'] = '法律法规列表';
        $this->view_data['list'] = $list;
        return view('legal.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        $this->view_data['meta_title'] = '新增法律法规';
        return view('legal.add', $this->view_data);
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

        //查询指定记录
        $row = Legal::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑法律法规';
        return view('legal.edit', $this->view_data);
    }

    /**
     * 新增|更新操作
     */
    public function update(Request $request){
        $id = $request->input('id', 0);

        $this->validate($request, [
            'title' => 'required|mb_max:40|mb_min:1',
            'url_pc' => 'required|url',
            'url_mobile' => 'required|url',
            'published_at' => 'required|date|before_or_equal:' . date('Y-m-d H:i:s'),
        ]);

        if ($id < 1) { //新增
            $legal = new Legal;
        } else {
            $legal = Legal::find($request->input('id'));
        }

        $legal->title = $request->input('title');
        $legal->url_pc = $request->input('url_pc');
        $legal->url_mobile = $request->input('url_mobile');
        $legal->published_at = $request->input('published_at');
        $legal->user_id = Auth::id();
        $result = $legal->save();
        
        if ($result) {
            return $this->success(route('legal_list'));
        }else{
            return $this->error('操作失败');
        }
    }

    /**
     * 删除操作
     */
    public function delete(Request $request){
        $ids = $request->input('id');
        $ids = explode(',', $ids);
        $ids = array_filter($ids);
        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        $result = Legal::destroy($ids);
    
        if ($result) {
            return $this->success(route('legal_list'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}