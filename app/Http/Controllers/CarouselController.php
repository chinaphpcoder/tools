<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Models\Carousel;
use Illuminate\Support\Facades\Auth;

/**
 * 链接管理
 */
class CarouselController extends Controller{

    /**
     * 列表
     */
    public function index(Request $request){
        $status_tmp = $request->input('status');

        $status = null;

        if (is_numeric($status_tmp) && $status_tmp == 0) {
            $status = 0;
        }

        if (is_numeric($status_tmp) && $status_tmp == 1) {
            $status = 1;
        }

        if (is_numeric($status_tmp) && $status_tmp == 2) {
            $status = 2;
        }

        if (is_null($status)) {
            $list = Carousel::orderBy('id', 'desc')->paginate(20);
        } else {
            $list = Carousel::orderBy('id', 'desc')->where('status', $status)->paginate(20);
        }

        //查询已上线的列表
        $online_list = Carousel::where('status', 1)->orderBy('sort', 'asc')->orderBy('id', 'desc')->get();

        $this->view_data['meta_title'] = '轮播图列表';
        $this->view_data['list'] = $list;
        $this->view_data['status'] = $status;
        $this->view_data['online_list'] = $online_list;
        return view('carousel.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        $this->view_data['meta_title'] = '新增轮播图';
        return view('carousel.add', $this->view_data);
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
        $row = Carousel::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑轮播图';
        return view('carousel.edit', $this->view_data);
    }

    /**
     * 新增|更新操作
     */
    public function update(Request $request){
        $id = $request->input('id', 0);

        //判断是否上传了图片
        $validate = [
            'title' => 'required|mb_max:40|mb_min:1',
            'started_at' => 'required|date',
            'ended_at' => 'required|date',
        ];

        $url = $request->input('url');
        if (!empty($url)) {
            $validate['url'] = 'required|url';
        } else {
            $url = '';
        }

        $image_url = $request->file('image_url');

        if ($id > 0 && is_null($image_url)) { //判断图片是否可用
            $new_image = false;
        } else {
            $validate['image_url'] = 'required|file|image';
            $new_image = true;
        }

        $this->validate($request, $validate);

        $started_at = $request->input('started_at');
        $ended_at = $request->input('ended_at');

        if ($ended_at <= $started_at) {
            return $this->error('结束时间不能比开始时间早');
        }

        if ($id < 1) { //新增
            $carousel = new Carousel;
        } else {
            $carousel = Carousel::find($request->input('id'));
        }

        $carousel->title = $request->input('title');
        $carousel->url = $url;
        $carousel->started_at = $started_at;
        $carousel->ended_at = $ended_at;
        $carousel->user_id = Auth::id();

        //处理图片
        if ($new_image) { //有图片
            try {
                $image_path = $image_url->store('carousels', 'oss');
            } catch (\Exception $e) {
                $image_path = $image_url->store('carousels', 'oss');
            }
            $carousel->image_url = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }
        $result = $carousel->save();
        
        if ($result) {
            return $this->success(route('carousel_list'));
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

        $row = Carousel::find($id);

        if (empty($row)) {
            return $this->error('暂未查询到相关记录');
        }

        $status = $row->status;

        if ($status == 1) {
            $row->status = 2;
        } else {
            $row->status = 1;

            //检测当前已经上线几个
            $count = Carousel::where('status', 1)->count();
            if ($count >= 5) {
                return $this->error('当前已发布5个轮播图，需下线一个后才能发布');
            }
        }

        if ($row->save()) {
            return $this->success(route('carousel_list', ['status' => $request->input('status')]));
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
        $result = Carousel::destroy($ids);
    
        if ($result) {
            return $this->success(route('carousel_list'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /**
     * 记录排序
     */
    public function order(Request $request){
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $ids = array_filter($ids, function($value){
            if ($value < 1) {
                return false;
            } else {
                return true;
            }
        });

        if (empty($ids)) {
            return [
                'status' => 0,
                'info' => '请选择要排序的记录'
            ];
        }

        foreach ($ids as $key => $value) {
            Carousel::where('id', $value)->update(['sort' => $key]);
        }

        return [
            'status' => 1,
            'info' => '操作成功'
        ];
    }
}