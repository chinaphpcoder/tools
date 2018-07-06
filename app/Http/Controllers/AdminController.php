<?php
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Users;

class AdminController extends Controller{

    /**
     * 后台用户列表
     */
    public function activity_menu(){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $list = DB::connection('cms')->table('admin_menus')->where('type','=','1')->groupBy('group')->orderBy('created_at','desc')->select('*')->get();

        $this->view_data['meta_title'] = '活动菜单管理';
        $this->view_data['list'] = $list;
        return view('admin.activity_menu', $this->view_data);
    }

    /**
     * 新增
     */
    public function activity_add(){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $this->view_data['meta_title'] = '新增活动菜单';
        return view('admin.activity_add', $this->view_data);
    }

       /**
     * 更新
     */
    public function activity_update(Request $request){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $id = $request->input('id');
        $activity_name = $request->input('activity_name');
        $identification = $request->input('identification');

        //判断名字
        if ( $activity_name == '') {
            return $this->error('活动名称不能为空');
        }
        //判断邮箱
        if ( $identification == '') {
            return $this->error('活动标识不能为空');
        }

        //查询邮箱是否重复
        $count = 0;
        if ($id <= 0) {
            $count = DB::connection('cms')->table('admin_menus')->where('group','=',$activity_name)->count();

        }

        if ( $count > 0 ) {
            return $this->error('该活动已存在');
        }

        $insert_data = [
            'title' => '活动奖品管理',
            'url' => "/activity/prize/manage/{$identification}",
            'group' => "{$activity_name}",
            'status' => '1',
            'type' => '1',
        ];

        $id = DB::connection('cms')
                ->table('admin_menus')
                ->insert($insert_data);

        $insert_data = [
            'title' => '中奖用户管理',
            'url' => "/activity/prize/winning_record/{$identification}",
            'group' => "{$activity_name}",
            'status' => '1',
            'type' => '1',
        ];

        $id = DB::connection('cms')
                ->table('admin_menus')
                ->insert($insert_data);

        if ( $id > 0 ) {
            return $this->success(route('admin.activity_menu'));
        } else {
            return $this->error('操作失败');
        }
    }

        /**
     * 删除
     */
    public function activity_delete(Request $request){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $group = $request->input('group');

        if (empty($group)) {
            return $this->error('请选择需要删除的记录');
        }


        //批量删除
        $result = DB::connection('cms')->table('admin_menus')->where('group','=',$group)->delete();
    
        if ($result) {
            return $this->success(route('admin.activity_menu'), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }


    /**
     * 判断当前用户ID
     */
    private function check_user(){
        $user_id = Auth::id();

        if ($user_id != 1) {
            return $this->error('禁止登录');
        } else {
            return true;
        }
    }
}