<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Models\AdminMenu;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $view_data = [];

    public function __construct() {
        //获取菜单列表
        $list = AdminMenu::where('status', 1)->orderBy('sort', 'desc')->orderBy('id', 'asc')->select(['title', 'url', 'group', 'tip'])->get();

        //菜单列表分组
        $tmp = [];
        foreach ($list as $key => $value) {
            $tmp[$value->group][] = $value;
        }
        //去除无效的分组
        $tmp = array_filter($tmp);

        $this->view_data['admin_menu'] = $tmp;
    }

    /**
     * 模仿TP的成功，只能返回页面
     */
    protected function success($url = 'javascript:window.history.go(-1);', $msg = '操作成功', $wait = 1){
        return view('public.dispatch_jump', ['status' => 1, 'url' => $url, 'msg' => $msg, 'wait' => $wait]);
    }

    /**
     * 模仿TP的失败，只能返回页面
     */
    protected function error($msg = '操作失败', $url = "javascript:window.history.go(-1);", $wait = 3){
        return view('public.dispatch_jump', ['status' => 0, 'url' => $url, 'msg' => $msg, 'wait' => $wait]);
    }
}
