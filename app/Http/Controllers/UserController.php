<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\User;

/**
 * 后台用户
 */
class UserController extends Controller{

    /**
     * 后台用户列表
     */
    public function index(){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $list = User::orderBy('id', 'desc')->paginate(20);

        $this->view_data['meta_title'] = '后台用户列表';
        $this->view_data['list'] = $list;
        return view('user.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $this->view_data['meta_title'] = '新增后台用户';
        return view('user.add', $this->view_data);
    }

    /**
     * 编辑
     */
    public function edit(Request $request){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        //获取ID
        $id = $request->input('id', 0);
        if ($id < 0) {
            return $this->error('没有获取到有效ID');
        }

        //查询指定记录
        $row = User::where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有查询到指定记录');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑友情链接';
        return view('user.edit', $this->view_data);
    }

    /**
     * 更新
     */
    public function update(Request $request){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        //判断名字
        if (empty($name)) {
            return $this->error('姓名不能为空');
        }
        //判断邮箱
        if (empty($email)) {
            return $this->error('邮箱不能为空');
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return $this->error('邮箱格式不正确');
        }
        //查询邮箱是否重复
        if ($id > 0) {
            $row = User::where('id', '!=', $id)->where('email', $email)->first();
        } else {
            $row = User::where('email', $email)->first();
        }
        if ($row) {
            return $this->error('该邮箱已经存在，请使用其他邮箱');
        }

        //密码
        if ($id < 1 && empty($password)) {
            return $this->error('密码不能为空');
        }

        if ($id > 0) {
            $user = User::find($id);
        } else {
            $user = new User;
        }

        $user->name = $name;
        $user->email = $email;
        if (!empty($password)) {
            $user->password = bcrypt($password);
        }

        if ($user->save()) {
            return $this->success(route('user.index'));
        } else {
            return $this->error('操作失败');
        }
    }

    /**
     * 删除
     */
    public function delete(Request $request){
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            return $check;
        }

        $ids = $request->input('id');
        $ids = explode(',', $ids);
        $ids = array_filter($ids);

        if (empty($ids)) {
            return $this->error('请选择需要删除的记录');
        }

        if (in_array(1, $ids) !== false) {
            return $this->error('禁止删除自己');
        }

        //批量删除
        $result = User::destroy($ids);
    
        if ($result) {
            return $this->success(route('user_list'), '删除成功');
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