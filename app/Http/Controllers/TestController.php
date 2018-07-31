<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\NewMenu;

/**
 * 测试
 */
class TestController extends Controller{

    /**
     * 树形目录
     */
    public function tree(){
        //获取菜单列表
        $list = NewMenu::orderBy('sort', 'desc')->orderBy('id', 'asc')->select(['id','pid','title', 'url'])->get();
        $items = json_decode(json_encode($list),true);
        $items = array_column($items, null,'id');
        $tree = [];
        foreach ($items as $item) {
            $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
        }
        $menus = isset($items[0]['son']) ? $items[0]['son'] : [];
        echo json_encode($items[0]['son']);
    }
}