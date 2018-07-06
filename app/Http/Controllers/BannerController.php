<?php

namespace App\Http\Controllers;

use App\Http\Models\Banners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    /**
     * 查看H5轮播图列表
     * @author 周瑶
     * @date 2017/11/11
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->input('status', null);

        if (is_null($status)) {
            $list = Banners::query()->orderBy(\DB::raw("field(status, '1','0','2')"))->orderBy('created_at', 'desc')->paginate(20);
        } else {
            $list = Banners::query()->where('status', $status)->orderBy(\DB::raw("field(status, '1','0','2')"))->orderBy('created_at', 'desc')->paginate(20);
        }
        //查询已上线列表
        $online_list = Banners::query()->where('status', 1)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();
        $this->view_data['meta_title'] = 'H5轮播图列表';
        $this->view_data['list'] = $list;
        $this->view_data['status'] = $status;
        $this->view_data['online_list'] = $online_list;
        return view('banner.index', $this->view_data);
    }

    /**
     * 新增H5轮播图
     * @author 周瑶
     * @date 2017/11/11
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->view_data['meta_title'] = '新增H5轮播图';
        return view('banner.add', $this->view_data);
    }


    /**
     * 处理新增|更新操作
     * @author 周瑶
     * @date 2017/11/11
     * @param Request $request
     */
    public function update(Request $request)
    {
        $id = $request->input('id', 0);
        // 判断是否上传了图片
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
            $this->error('结束时间不能比开始时间早');
        }
        if ($id < 1) { //新增
            $banner = new Banners();
        } else {
            $banner = Banners::query()->find($request->input('id')); //查询
        }
        $banner->title = $request->input('title');
        $banner->url = $url;
        $banner->started_at = $started_at;
        $banner->ended_at = $ended_at;
        $banner->user_id = Auth::id();

        //处理图片
        if ($new_image) { //有图片
            try {
                $image_path = $image_url->store('banner', 'oss');
            } catch (\Exception $e) {
                $image_path = $image_url->store('banner', 'oss');
            }
            $banner->image_url = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }
        $result = $banner->save(); //保存数据

        if ($result) {
            return $this->success(route('banner_index'));
        } else {
            return $this->error('操作失败');
        }
    }

    /**
     * 编辑H5轮播图视图
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function edit(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        //查询指定记录
        $row = Banners::query()->where('id', '=', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑H5轮播图';
        return view('banner.edit', $this->view_data);
    }

    /**
     * 删除H5轮播图
     * @author 周瑶
     * @date 2017/11/11
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('id'); //接收条件
        $ids = explode(',', $ids); //把字符串条件分割为数组

        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        $result = Banners::destroy($ids);
        if ($result) {
            return $this->success(route('banner_index'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /**
     * 处理H5轮播图上线与下线操作
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function status(Request $request)
    {
        $id = $request->input('id', 0); //接收条件
        $id = intval($id);  //转换int型
        
        if ($id < 1) {
            return $this->error('请选择需要操作的记录');
        }
        
        $row = Banners::query()->find($id); //取出要查询的数据
        
        if (empty($row)) {
            return $this->error('暂未查询到相关记录');
        }

        $status = $row->status;

        if ($status == 1) {
            $row->status = 2;
        } else {
            $row->status = 1;
            //检测当前已经上线几个
            $count = Banners::query()->where('status', '=', 1)->count();
            if ($count >= 5) {
                return $this->error('当前已发布5个轮播图，需要下线一个后才能发布');
            }
        }

        if ($row->save()) { //保存成功执行
            return $this->success(route('banner_index', ['status' => $request->input('status')]));
        } else {
            return $this->success('操作失败');
        }
    }

    public function order(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $ids = array_filter($ids, function ($value) {
            if ($value < 1) {
                return false;
            } else {
                return true;
            }
        });

        if (empty($ids)) {
            return ['status' => 0, 'info' => '请选择要排序的记录'];
        }

        foreach ($ids as $key => $value) {
            Banners::query()->where('id', '=', $value)->update(['sort' => $key]);
        }

        return ['status' => 1, 'info' => '操作成功'];
    }

}
