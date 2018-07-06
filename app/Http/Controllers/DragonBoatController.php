<?php

namespace App\Http\Controllers;

use App\Http\Models\PCBanners as Banners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class DragonBoatController extends Controller
{
    private $identification = 'DB20180618';

    /**
     * 中奖概率设置
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function setting(Request $request)
    {
        $is_test = $request->input('is_test', '0');
        $this->view_data['is_test'] = $is_test;
        $this->view_data['meta_title'] = '奖品概率管理';
        return view('dragonboat.setting', $this->view_data);
    }

    public function userList(Request $request)
    {
        $is_test = $request->input('is_test', '0');
        $user_id = $request->input('user_id', null);
        $mobile = $request->input('mobile', null);
        $prize_id = $request->input('prize_id', 0);

        $activity_info = DB::connection('vault')
                    ->table('vault_activity')
                    ->where('vault_activity.activity_identification','=',$this->identification)
                    ->where('vault_activity.is_test','=',$is_test)
                    ->first();
        $activity_id = 0;
        if( isset($activity_info->id) ) {
            $activity_id = $activity_info->id;
        }
        if( $activity_id > 0 ) {

            $prize = DB::connection('vault')
                    ->table('vault_activity_prize')
                    ->where('activity_id', '=', $activity_id)
                    ->get();
            $prize = json_decode(json_encode($prize), true);
            $prize_new = [];
            foreach ($prize as $key => $value) {
                $prize_new[$value['id']] = $value;
            }
            //获取参与抽奖的用户数据
            $db_user = DB::connection('vault')->table('vault_user_prize_log as up');

            //查询用户名对应的记录
            if (!empty($user_id)) {
                $search_user = $DB->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
                if( !isset($search_user[0]) ) {
                    $tmp_user_id = 0;
                } else {
                    $tmp_user_id = $search_user[0];
                }
                $db_user->where('user_id', '=', $tmp_user_id);
            }

            //查询手机号对应的记录
            if (!empty($mobile)) {
                $search_mobile = $DB->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
                if( !isset($search_mobile[0]) ) {
                    $tmp_user_id = 0;
                } else {
                    $tmp_user_id = $search_mobile[0];
                }
                $db_user->where('user_id', '=', $tmp_user_id);
            }

            if (!empty($prize_id)) {
                $db_user->where('prize_id', '=', $prize_id);
            }
            $user =$db_user->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
            ->where('up.activity_id', '=', $activity_id)
            ->select([
                'up.user_id',
                'up.prize_id',
                'up.time',
                'up.state',
                'u.mobile',
                'u.user_name',
                'u.real_name',

            ])->get();
            $user = json_decode(json_encode($user), true);

            $user_id_arr = array_unique(array_column($user, 'user_id'));


            //获取用户的收货地址
            $user_address = DB::connection('vault')->table('vault_address')
                            ->where('activity_id', '=', $activity_id)
                            ->whereIn('user_id', $user_id_arr)
                            ->groupBy('user_id')
                            ->select([
                                DB::raw('any_value(phone) as phone'),
                                DB::raw('any_value(address) as address'),
                                DB::raw('any_value(name) as name'),
                                'user_id'])
                            ->get();
            $user_address = json_decode(json_encode($user_address), true);
            $user_address_new = [];
            foreach ($user_address as $key => $val) {
                $user_address_new[$val['user_id']] = [
                    'phone' => $val['phone'],
                    'address' => $val['address'],
                    'name' => $val['name'],
                ];
            }
            unset($user_address);
            //根据用户取出奖品信息并去重用户数据
            $user_new = [];
            
            foreach ($user as $key => $val) {
                $user_new[$val['user_id']]['user_id'] = $val['user_id'];
                $user_new[$val['user_id']]['time'] = $val['time'];
                $user_new[$val['user_id']]['prize'][] = $prize_new[$val['prize_id']];
                $user_new[$val['user_id']]['user_name'] = $val['user_name'];
                $user_new[$val['user_id']]['real_name'] = $val['real_name'];
                $user_new[$val['user_id']]['mobile'] = $val['mobile'];

                $user_new[$val['user_id']]['phone'] = isset($user_address_new[$val['user_id']]['phone']) ? $user_address_new[$val['user_id']]['phone'] : '';
                $user_new[$val['user_id']]['name'] = isset($user_address_new[$val['user_id']]['name'])? $user_address_new[$val['user_id']]['name'] : '';
                $user_new[$val['user_id']]['address'] = isset($user_address_new[$val['user_id']]['address']) ? $user_address_new[$val['user_id']]['address'] : '';
            }

            echo json_encode($user_new);
            exit;


            $this->view_data['meta_title'] = '中奖用户管理';
            $this->view_data['prize'] = $prize_new;
            return view('dragonboat.user_list', $this->view_data);
        }
        echo json_encode($activity_info);
        exit;

        $DB = DB::connection('vault');
        //获取奖品
        $prize = $DB->table('vault_prize')->where('activity_id', '=', $activity_id)
            ->select(['id', 'prize_name', 'detail','price_ext2'])
            ->get();
        $prize = json_decode(json_encode($prize), true);
        $prize_new = [];
        foreach ($prize as $key => $val) {
            $val['detail'] = "累计年出借金额".number_format($val['price_ext2'],0)."元";
            $prize_new[$val['id']] = $val;
        }
        $this->view_data['prize'] = $prize;
        unset($prize);

        //获取参与抽奖的用户数据
        $db_user = $DB->table('vault_user_prize_log as up');

        //查询用户名对应的记录
        if (!empty($user_id)) {
            $search_user = $DB->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
            if( !isset($search_user[0]) ) {
                $tmp_user_id = 0;
            } else {
                $tmp_user_id = $search_user[0];
            }
            $db_user->where('user_id', '=', $tmp_user_id);
        }

        //查询手机号对应的记录
        if (!empty($mobile)) {
            $search_mobile = $DB->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
            if( !isset($search_mobile[0]) ) {
                $tmp_user_id = 0;
            } else {
                $tmp_user_id = $search_mobile[0];
            }
            $db_user->where('user_id', '=', $tmp_user_id);
        }

        if (!empty($prize_id)) {
            $db_user->where('prize_id', '=', $prize_id);
        }

        if ( $state != 3) {
            $db_user->where('state', '=', $state);
        }



        $user =$db_user->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
            ->where('up.activity_id', '=', $activity_id)
            ->where('up.type', '=', 0)
            ->select([
                'up.user_id',
                'up.prize_id',
                'up.time',
                'up.state',
                'u.mobile',
                'u.user_name',
                'u.real_name',

            ])->get();
        $user = json_decode(json_encode($user), true);

        $user_id_arr = array_unique(array_column($user, 'user_id'));



        //获取用户的收货地址
        $user_address = $DB->table('vault_address')
            ->where('activity_id', '=', $activity_id)
            ->whereIn('user_id', $user_id_arr)
            ->groupBy('user_id')
            ->select([
                DB::raw('any_value(phone) as phone'),
                DB::raw('any_value(address) as address'),
                DB::raw('any_value(name) as name'),
                'user_id'])
            ->get();
        $user_address = json_decode(json_encode($user_address), true);
        $user_address_new = [];
        foreach ($user_address as $key => $val) {
            $user_address_new[$val['user_id']] = [
                'phone' => $val['phone'],
                'address' => $val['address'],
                'name' => $val['name'],
            ];
        }
        unset($user_address);
        //根据用户取出奖品信息并去重用户数据
        $user_new = [];
        
        foreach ($user as $key => $val) {
            $user_new[$val['user_id']]['user_id'] = $val['user_id'];
            $user_new[$val['user_id']]['time'] = $val['time'];
            $user_new[$val['user_id']]['prize'][] = $prize_new[$val['prize_id']];
            $state = "未知";
            switch ($val['state']) {
                case 0:
                    $state = '未获得';
                    break;
                case 1:
                    $state = '预计已获得';
                    break;
                case 2:
                    $state = '已失效';
                    break;
            }
            $user_new[$val['user_id']]['state'][] = $state;
            $user_new[$val['user_id']]['user_name'] = $val['user_name'];
            $user_new[$val['user_id']]['real_name'] = $val['real_name'];
            $user_new[$val['user_id']]['mobile'] = $val['mobile'];
//            $user_new[$val['user_id']]['total'] = isset($user_invest[$val['user_id']]) ? number_format($user_invest[$val['user_id']] / 100, 2) : '';
            $user_new[$val['user_id']]['phone'] = isset($user_address_new[$val['user_id']]['phone']) ? $user_address_new[$val['user_id']]['phone'] : '';
            $user_new[$val['user_id']]['name'] = isset($user_address_new[$val['user_id']]['name'])? $user_address_new[$val['user_id']]['name'] : '';
            $user_new[$val['user_id']]['address'] = isset($user_address_new[$val['user_id']]['address']) ? $user_address_new[$val['user_id']]['address'] : '';
        }


        //单独求用户累计出借年金额
        $num = 1;
        foreach ($user_new as $key => &$val) {
            $end_time = date('Y-m-d 23:59:59', strtotime($val['time'] . ' + 30 day'));
            //获取用户投资总金额
            $user_invest = $DB->table('vault_user_invest_trade_log')
                ->where('user_id', $key)
                ->where('type', '=', 1)
                ->where('deal_term', '>=', 3)
                ->where('in_time', '>=', $val['time'])
                ->where('in_time', '<=', $end_time)
                ->groupBy('user_id')
                ->pluck(DB::raw('sum(in_money * deal_term / 12) as in_money'), 'user_id')->toArray();
            $val['total'] = empty($user_invest) ? 0 : number_format($user_invest[$key] / 100, 2);
            $val['num'] = $num;
            $num++;
        }

        $this->view_data['data'] = $user_new;
        $this->view_data['meta_title'] = '中奖用户管理';
        return view('activity.user_address', $this->view_data);
    }


    public function prize_list(Request $request)
    {
        $is_test = $request->input('is_test', '0');
        $lists = DB::connection('vault')
                    ->table('vault_activity_prize')
                    ->join('vault_activity', 'vault_activity_prize.activity_id', '=', 'vault_activity.id')
                    ->where('vault_activity.activity_identification','=',$this->identification)
                    ->where('vault_activity.is_test','=',$is_test)
                    ->select(['vault_activity_prize.id','vault_activity_prize.prize_name','vault_activity_prize.obtain_probability','vault_activity.is_test'])
                    ->get();
        // $lists = DB::connection('vault')->table('vault_activity_prize')->select()->get();
        $lists = json_decode(json_encode($lists),true);
        $count = 1;
        foreach ($lists as $key=>$value) {
            $lists[$key]['obtain_probability'] = $value['obtain_probability'] / 100 ."%";
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

    public function probability_setting(Request $request)
    {
        $id = $request->input('id');
        $info = DB::connection('vault')->table('vault_activity_prize')->where('id','=',$id)->first();
        $this->view_data['obtain_probability'] = $info->obtain_probability;
        $this->view_data['id'] = $id;
        return view('dragonboat.probability_setting', $this->view_data);
    }

    public function probability_update(Request $request){

        $id = $request->input('id');
        $obtain_probability = $request->input('obtain_probability');
        if($obtain_probability <0 || $obtain_probability > 100) {
            return $this->error('中奖概率须在0-100之间');
        }
        $obtain_probability = (int)floor($obtain_probability * 100 );

        $status = DB::connection('vault')->table('vault_activity_prize')->where('id','=',$id)->update(['obtain_probability' => $obtain_probability]);
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
