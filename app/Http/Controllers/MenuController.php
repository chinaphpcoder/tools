<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Models\Menus;

/**
 * 菜单管理
 */
class MenuController extends Controller{

    /**
     * 列表
     */
    public function lists(){
        $list = Menus::orderBy('id', 'desc')->paginate(20);
        $this->view_data['meta_title'] = '菜单列表';
        $this->view_data['list'] = $list;
        return view('menu.index', $this->view_data);
    }

    /**
     * 新增
     */
    public function add(){
        $this->view_data['meta_title'] = '新增菜单';
        return view('menu.add', $this->view_data);
    }

    /**
     * 更新操作
     */
    public function update(Request $request){
        $id = $request->input('id', 0);
        if ($id < 1) {
            $this->validate($request, [
                'title' => 'required|max:20',
                'url' => 'required|unique:admin_menus|max:100|string',
                'group' => 'required|string|max:20',
                'status' => 'required|integer',
                'sort' => 'min:0',
            ]);

            $sort = $request->input('sort');
            $tip = $request->input('tip');

            $admin_menu = new Menus;
            $admin_menu->title = $request->input('title');
            $admin_menu->sort = isset($sort) ? $sort : 0;
            $admin_menu->url = '/' . ltrim($request->input('url'), '/');
            $admin_menu->tip = isset($tip) ? $tip : '';
            $admin_menu->group = $request->input('group');
            $admin_menu->status = $request->input('status');

            if ($admin_menu->save()) {
                return $this->success(url('menu/index'));
            }else{
                echo 'failed';
            }
        }else{
            echo '更新';
        }
    }
}