<?php
namespace App\Http\Controllers;
use App\Http\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 合作机构
 */
class GoodFriendController extends Controller{
    /**
     * 合作机构列表
     */
    public function index()
    {
        $list = Friend::orderBy('created_at', 'desc')->with('user')->paginate(20);
        $this->view_data['meta_title'] = '合作机构列表';
        $this->view_data['list'] = $list;
        return view('friend.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        $this->view_data['meta_title'] = '新增合作机构';
        return view('friend.add', $this->view_data);
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
        $row = Friend::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑合作机构';
        return view('friend.edit', $this->view_data);
    }

    /**
     * 更新操作
     * @return array
     */
    public function update(Request $request){
        $id = $request->input('id', 0);

        $validate = [
            'title' => 'required|mb_max:20',
            'description' => 'required|mb_min:1|mb_max:300|string',
            'sort' => 'min:0',
        ];

        //PC端首页展示标准图
        $pc_index_pic = $request->file('pc_index_pic');
        if ($id > 0 && is_null($pc_index_pic)) { //判断图片是否可用
            $pc_index_pic_status = false;
        } else {
            $validate['pc_index_pic'] = 'required|file|image';
            $pc_index_pic_status = true;
        }

        //PC端首页展示小尺寸图
        $pc_index_small_pic = $request->file('pc_index_small_pic');
        if ($id > 0 && is_null($pc_index_small_pic)) { //判断图片是否可用
            $pc_index_small_pic_status = false;
        } else {
            $validate['pc_index_small_pic'] = 'required|file|image';
            $pc_index_small_pic_status = true;
        }

        //PC端合作机构标准图
        $pc_cooperate_pic = $request->file('pc_cooperate_pic');
        if ($id > 0 && is_null($pc_cooperate_pic)) { //判断图片是否可用
            $pc_cooperate_pic_status = false;
        } else {
            $validate['pc_cooperate_pic'] = 'required|file|image';
            $pc_cooperate_pic_status = true;
        }

        //PC端合作机构小尺寸图
        $pc_cooperate_small_pic = $request->file('pc_cooperate_small_pic');
        if ($id > 0 && is_null($pc_cooperate_small_pic)) { //判断图片是否可用
            $pc_cooperate_small_pic_status = false;
        } else {
            $validate['pc_cooperate_small_pic'] = 'required|file|image';
            $pc_cooperate_small_pic_status = true;
        }

        //M站图片
        $m_pic = $request->file('m_pic');
        if ($id > 0 && is_null($m_pic)) { //判断图片是否可用
            $m_pic_status = false;
        } else {
            $validate['m_pic'] = 'required|file|image';
            $m_pic_status = true;
        }

        $this->validate($request, $validate);

        if ($id < 1) {
            $friend = new Friend;
        } else {
            $friend = Friend::find($id);
        }

        $friend->title = $request->input('title');
        $friend->description = $request->input('description');
        $friend->sort = $request->input('sort');
        $friend->user_id = Auth::id();

        //处理首页展示标准图
        if ($pc_index_pic_status) { //有图片
            try {
                $image_path = $pc_index_pic->store('friend', 'oss');
            } catch (\Exception $e) {
                $image_path = $pc_index_pic->store('friend', 'oss');
            }

            $friend->pc_index_pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        //处理首页展示小尺寸图
        if ($pc_index_small_pic_status) { //有图片
            try {
                $image_path = $pc_index_small_pic->store('friend', 'oss');
            } catch (\Exception $e) {
                $image_path = $pc_index_small_pic->store('friend', 'oss');
            }

            $friend->pc_index_small_pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        //处理合作机构标准图
        if ($pc_cooperate_pic_status) { //有图片
            try {
                $image_path = $pc_cooperate_pic->store('friend', 'oss');
            } catch (\Exception $e) {
                $image_path = $pc_cooperate_pic->store('friend', 'oss');
            }

            $friend->pc_cooperate_pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        //处理合作机构小尺寸图
        if ($pc_cooperate_small_pic_status) { //有图片
            try {
                $image_path = $pc_cooperate_small_pic->store('friend', 'oss');
            } catch (\Exception $e) {
                $image_path = $pc_cooperate_small_pic->store('friend', 'oss');
            }

            $friend->pc_cooperate_small_pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        //处理M站图片
        if ($m_pic_status) { //有图片
            try {
                $image_path = $m_pic->store('friend', 'oss');
            } catch (\Exception $e) {
                $image_path = $m_pic->store('friend', 'oss');
            }
            $friend->m_pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        $result = $friend->save();
        
        if ($result) {
            return $this->success(route('friend_list'));
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
        $result = Friend::destroy($ids);
    
        if ($result) {
            return $this->success(route('friend_list'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}
