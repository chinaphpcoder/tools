<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Models\Link;
use Illuminate\Support\Facades\Auth;

/**
 * 链接管理
 */
class LinkController extends Controller{

    /**
     * 列表
     */
    public function index(){
        $list = Link::orderBy('id', 'desc')->with('user')->paginate(20);

        $this->view_data['meta_title'] = '友情链接列表';
        $this->view_data['list'] = $list;
        return view('link.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        $this->view_data['meta_title'] = '新增友情链接';
        return view('link.add', $this->view_data);
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
        $row = Link::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑友情链接';
        return view('link.edit', $this->view_data);
    }

    /**
     * 新增|更新操作
     */
    public function update(Request $request){
        $id = $request->input('id', 0);

        //判断是否上传了图片
        $validate = [
            'title' => 'required|mb_max:40|mb_min:1',
            'url' => 'required|url',
            'sort' => 'min:0',
        ];

        $image_url = $request->file('image_url');

        if ($id > 0 && is_null($image_url)) { //判断图片是否可用
            $image_url_status = false;
        } else {
            $validate['image_url'] = 'required|file|image';
            $image_url_status = true;
        }

        $image_small_url = $request->file('image_small_url');
        if ($id > 0 && is_null($image_small_url)) { //判断图片是否可用
            $image_small_url_status = false;
        } else {
            $validate['image_small_url'] = 'required|file|image';
            $image_small_url_status = true;
        }

        $this->validate($request, $validate);

        if ($id < 1) { //新增
            $link = new Link;
        } else {
            $link = Link::find($request->input('id'));
        }

        $link->title = $request->input('title');
        $link->url = $request->input('url');
        $link->sort = $request->input('sort');
        $link->user_id = Auth::id();

        //处理图片
        if ($image_url_status) { //有图片
            try {
                $image_path = $image_url->store('links', 'oss');
            } catch (\Exception $e) {
                $image_path = $image_url->store('links', 'oss');
            }

            if (empty($image_path)) {
                return $this->error('上传图片失败，请重试');
            }
            $link->image_url = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        //处理图片
        if ($image_small_url_status) { //有图片
            try {
                $image_path = $image_small_url->store('links', 'oss');
            } catch (\Exception $e) {
                $image_path = $image_small_url->store('links', 'oss');
            }

            if (empty($image_path)) {
                return $this->error('上传图片失败，请重试');
            }
            $link->image_small_url = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        $result = $link->save();
        
        if ($result) {
            return $this->success(route('link_list'));
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
        $result = Link::destroy($ids);
    
        if ($result) {
            return $this->success(route('link_list'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

}