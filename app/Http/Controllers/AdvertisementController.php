<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Models\Advertisement;
use App\Http\Models\AdvertisementPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertisementController extends Controller
{
    /**
     * 查看PC轮播图列表
     * @author 周瑶
     * @date 2017/11/11
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fixed(Request $request)
    {
        $position_name = ['recharge-result' => '充值结果页','withdraw-result' => '提现结果页','invest-result' => '投资结果页'];
        $publish_status = $request->input('publish_status', null);
        $list = Advertisement::query()->where('is_delete', "0")->orderBy('created_at', 'desc')->paginate(20);

        foreach ($list as $keys => &$values) {
            $positions = '';
            foreach ($values->position as $key => $value) {
                if( isset($position_name[$value->display_position]) ) {
                    $positions .= $position_name[$value->display_position] . "、";
                }             
            }
            $values->display_position = str_replace("、", "<br>", trim($positions,"、"));
        }


        $this->view_data['meta_title'] = '固定图管理';
        $this->view_data['list'] = $list;
        $this->view_data['publish_status'] = $publish_status;
        // $this->view_data['online_list'] = $online_list;
        return view('fixed.index', $this->view_data);
    }

    /**
     * 新增PC轮播图
     * @author 周瑶
     * @date 2017/11/11
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        $this->view_data['meta_title'] = '新增固定图';
        return view('fixed.add', $this->view_data);
    }

    public function create(Request $request)
    {

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
        $pic = $request->file('pic');
        $validate['pic'] = 'required|file|image';
        $new_image = true;
    
        $this->validate($request, $validate);
        $started_at = $request->input('started_at');
        $ended_at = $request->input('ended_at');
        if ($ended_at <= $started_at) {
            $this->error('结束时间不能比开始时间早');
        }
        $positions = $request->input('display_position',[]);
        if( $positions == null ) {
            $this->error('至少选择一个显示位置');
        }
        //开始事务
        DB::beginTransaction();
        $ad = new Advertisement();
        $data = [];
        $data['title'] = $request->input('title');
        $data['url'] = $url;
        $data['started_at'] = $started_at;
        $data['ended_at'] = $ended_at;
        $data['admin_user_id'] = Auth::id();

        //处理图片
        if ($new_image) { //有图片
            try {
                $image_path = $pic->store('pcbanner', 'oss');
            } catch (\Exception $e) {
                $image_path = $pic->store('pcbanner', 'oss');
            }
            $data['pic'] = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        $id = $ad::insertGetId($data);
        if( $id <= 0 ) {
            DB::rollBack();
            return $this->error('插入主数据失败');
        }
        $ap = new AdvertisementPosition();
        foreach ($positions as $key => $value) {
            $pid = $ap::insertGetId(['ad_id' => $id ,'display_position' => $value]);
            if( $pid <= 0 ) {
                DB::rollBack();
                return $this->error('插入位置数据失败');
            }
        }

        DB::commit();

        return $this->success(route('fixed_index'));
    }


    /**
     * 处理新增|更新操作
     * @author 周瑶
     * @date 2017/11/11
     * @param Request $request
     * @return mixed
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
        $pic = $request->file('pic');
        if ($id > 0 && is_null($pic)) { //判断图片是否可用
            $new_image = false;
        } else {
            $validate['pic'] = 'required|file|image';
            $new_image = true;
        }
        $this->validate($request, $validate);
        $started_at = $request->input('started_at');
        $ended_at = $request->input('ended_at');
        if ($ended_at <= $started_at) {
            $this->error('结束时间不能比开始时间早');
        }
        $positions = $request->input('display_position',[]);
        if( $positions == null ) {
            $this->error('至少选择一个显示位置');
        }

        //开始事务
        DB::beginTransaction();
        $ad = Advertisement::query()->find($request->input('id')); //查询

        $ad->title = $request->input('title');
        $ad->url = $url;
        $ad->started_at = $started_at;
        $ad->ended_at = $ended_at;
        $ad->admin_user_id = Auth::id();

        //处理图片
        if ($new_image) { //有图片
            try {
                $image_path = $pic->store('pcbanner', 'oss');
            } catch (\Exception $e) {
                $image_path = $pic->store('pcbanner', 'oss');
            }
            $ad->pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }
        $result = $ad->save(); //保存数据

        if( $result !== true ) {
            DB::rollBack();
            return $this->error('保存数据失败');
        }
        $ap = new AdvertisementPosition();
        foreach ($positions as $key => $value) {
            $status = $ap::firstOrCreate(['ad_id' => $id ,'display_position' => $value],['ad_id' => $id ,'display_position' => $value]);
            // var_dump($status);
            // $pid = $ap::insertGetId(['ad_id' => $id ,'display_position' => $value]);
            // if( $pid <= 0 ) {
            //     DB::rollBack();
            //     return $this->error('插入位置数据失败');
            // }
        }
        $ap::where('ad_id',$id)->whereNotIn('display_position',$positions)->delete();

        DB::commit();

        return $this->success(route('fixed_index'));
    }

    /**
     * 编辑PC轮播图视图
     * @param  Request $request [description]
     * @return mixed
     */
    public function edit(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        //查询指定记录
        $row = Advertisement::query()->where('id', '=', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }
        $positions = [];
        foreach ($row->position as $key => $value) {
            $positions[] = $value->display_position;
        }
        $this->view_data['positions'] = $positions;
        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑固定图';
        return view('fixed.edit', $this->view_data);
    }

    /**
     * 删除PC轮播图
     * @author 周瑶
     * @date 2017/11/11
     * @param  Request $request [description]
     * @return
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('id'); //接收条件
        $ids = explode(',', $ids); //把字符串条件分割为数组

        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        //批量删除
        //开始事务
        DB::beginTransaction();
        $result = Advertisement::destroy($ids);
        if( !$result ) {
            DB::rollBack();
            return $this->error('删除失败');
        }
        $result = AdvertisementPosition::whereIn('ad_id',$ids)->delete();
        if( $result === false ){
            DB::rollBack();
            return $this->error('删除失败');
        }

        DB::commit();
        return $this->success(route('fixed_index'), '删除成功');
    }

    /**
     * 处理H5轮播图上线与下线操作
     * @param  Request $request [description]
     * @return mixed
     */
    public function status(Request $request)
    {
        $id = $request->input('id', 0); //接收条件
        $id = intval($id);  //转换int型
        
        if ($id < 1) {
            return $this->error('请选择需要操作的记录');
        }
        
        $row = Advertisement::query()->find($id); //取出要查询的数据
        
        if (empty($row)) {
            return $this->error('暂未查询到相关记录');
        }

        $publish_status = $row->publish_status;

        if ($publish_status == 1) {
            $row->publish_status = 2;
        } else {
            $row->publish_status = 1;
            //检测当前已经上线几个
            $count = Advertisement::query()->where('publish_status', '=', 1)->count();
            if ($count >= 5) {
                return $this->error('当前已发布5个轮播图，需要下线一个后才能发布');
            }
        }

        if ($row->save()) { //保存成功执行
            return $this->success(route('fixed_index', ['publish_status' => $request->input('publish_status')]));
        } else {
            return $this->success('操作失败');
        }
    }

    /**
     * 操作轮播图排序
     * User: zhouyao
     * Date: 2018/5/17
     * Time: 下午2:43
     * @param Request $request
     * @return array
     */
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
