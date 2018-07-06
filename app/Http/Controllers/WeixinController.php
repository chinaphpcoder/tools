<?php

namespace App\Http\Controllers;

use App\Http\Models\PCBanners as Banners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class WeixinController extends Controller
{
    private $identification = 'DB20180618';

    /**
     * 中奖概率设置
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function autoReply(Request $request)
    {
        $this->view_data['meta_title'] = '微信自动回复管理';
        return view('weixin.auto_reply', $this->view_data);
    }

    public function replySetting(Request $request)
    {
        $id = $request->input('id');
        $info = DB::connection('cms')->table('cms_weixin_reply')->where('id','=',$id)->first();
        $this->view_data['content'] = $info->content;
        $this->view_data['id'] = $id;
        return view('weixin.reply_setting', $this->view_data);
    }

    public function replyList(Request $request)
    {
        $lists = DB::connection('cms')
                    ->table('cms_weixin_reply')
                    ->where('type','=',1)
                    ->select()
                    ->get();
        $lists = json_decode(json_encode($lists),true);
        $count = 1;
        foreach ($lists as $key=>$value) {
            if( $value['publish_status'] == 1) {
                $lists[$key]['publish_status_name'] = '已发布';
            } else {
                $lists[$key]['publish_status_name'] = '未发布';
            }
            $lists[$key]['pid'] = $count;
            $count++;
        }
        $result = [];
        $result['code'] = 0;
        $result['msg'] = '';
        $result['count'] = count($lists);
        $result['data'] = $lists;
        return $result;
    }

    public function replyUpdate(Request $request)
    {
        $content = $request->input('content');
        $cotent = urldecode($content);
        $id = $request->input('id');

        $status = DB::connection('cms')->table('cms_weixin_reply')->where('id','=',$id)->update(['content' => $content]);
        if($status === false) {
            return $this->error('更新失败');
        }

        return $this->success('更新成功');
    }

    public function success($msg='success',$data='',$code='200'){
        $result = array();
        $result['code'] = $code;
        $result['msg'] = $msg;
        $result['data'] = $data;
        return $result;
    }
    public function error($msg='error',$data='',$code='400'){
        $result = array();
        $result['code'] = $code;
        $result['msg'] = $msg;
        $result['data'] = $data;
        return $result;
    }

}
