<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;

class ActivityController extends Controller
{
    /**
     * 管理活动用户文件列表
     * User: zhouyao
     * Date: 2018/5/22
     * Time: 上午10:49
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $list = DB::connection('vault')->table('vault_activity_user_list')
            ->where('activity_identification', '=', 'zhuanpan')
            ->where('is_delete', '=', 0)
            ->select(['id', 'original_filename', 'upload_time', 'admin_name', 'filename'])
            ->paginate(20);

        $this->view_data['list'] = $list;
        $this->view_data['meta_title'] = '专属用户管理';
        return view('activity.index', $this->view_data);
    }

    /**
     * 下载用户文件表格
     * User: zhouyao
     * Date: 2018/5/23
     * Time: 下午2:57
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request)
    {
        $id = $request->input('id', 0);
        $row = DB::connection('vault')->table('vault_activity_user_list')
            ->where('id', '=', $id)
            ->select('filename')
            ->first();
        return response()->download($row->filename);
    }

    /**
     * 删除用户文件同时删除文件里面的记录数据
     * User: zhouyao
     * Date: 2018/5/23
     * Time: 下午3:01
     * @param Request $request
     * @throws \Throwable
     * @return mixed
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');
        DB::transaction(function () use ($id) {
            DB::connection('vault')->table('vault_activity_user_list')->delete($id);
            DB::connection('vault')->table('vault_activity_user_list_details')->where('file_id', '=', $id)->delete();
        });
        return $this->success(route('user_list'), '文件删除成功');
    }



    /**
     * 显示上传Excel的视图
     * User: zhouyao
     * Date: 2018/5/22
     * Time: 下午3:26
     */
    public function create()
    {
        return view('activity.create');
    }

    /**
     * 处理附件上传
     * User: zhouyao
     * Date: 2018/5/22
     * Time: 下午3:46
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        $old_name = $file->getClientOriginalName(); //获取文件名
        $extension = $file->getClientOriginalExtension(); //获取文件后缀名
        //检测文件后缀是否合法
        if (!in_array($extension, ['xls', 'csv', 'xlsx'])) {
            return $this->error('上传的附件不符合，请重新上传');
        }
        $filePath = $file->store('exports');
        $filePath = storage_path('app/' . $filePath);

        $user_list_data = [
            'activity_identification' => 'zhuanpan',
            'original_filename' => $old_name,
            'filename' => $filePath,
            'is_delete' => 0,
            'upload_time' => date('Y-m-d H:i:s'),
            'admin_name' => \Auth::user()->name
        ];

        Excel::load($filePath, function($reader) use ($user_list_data) {
            set_time_limit(0);
            $reader = $reader->getSheet(0);//excel第一张sheet
            $data = $reader->toArray();
            DB::connection('vault')->beginTransaction();
            if (!empty($data)) {
                //文件写入表
                $connection   = DB::connection('vault');
                $user_list_id = $connection->table('vault_activity_user_list')->insertGetId($user_list_data);
                if( $user_list_id <= 0 ) {
                    DB::connection('vault')->rollBack();
                    exit('<script>alert("附件处理失败");</script>');
                }
                //把表格数据批量写入到表
                $total_count = count($data);
                $count = 0;
                $page_size = 10000;
                $mobiles = [];

                $flag = true;
                foreach ($data as $key => $value) {
                    $count ++;
                    if( $mobile = check_mobile(trim($value[0])) ) {
                        $mobiles[] = $mobile;
                    }

                    if( $count % $page_size == 0 || $count == $total_count ) {
                        if( count($mobiles) > 0 ) {
                            $details_arr = DB::connection('vault')
                                            ->table("vault_user")
                                            ->whereIn("mobile",$mobiles)
                                            ->select([
                                                DB::raw("id as 'user_id'"),
                                                DB::raw("mobile as 'mobile'"),
                                                DB::raw("'{$user_list_id}' as 'file_id'"),
                                            ])->get();
                            $details_arr = json_decode(json_encode($details_arr),true);
                            if( $details_arr ) {
                                $status = DB::connection('vault')->table('vault_activity_user_list_details')->insert($details_arr);
                                if($status == false) {
                                    $flag = false;
                                    break;
                                }
                            }
                        }
                    }
                }
                if($flag == false) {
                    DB::connection('vault')->rollBack();
                    exit('<script>alert("附件处理失败");</script>');
                }
                DB::connection('vault')->commit();
                exit('<script>alert("附件处理成功");parent.location.reload();</script>');
            }
            exit('<script>alert("附件为空");</script>');
        });

    }


    /**
     * 中奖用户管理
     * User: zhouyao
     * Date: 2018/5/22
     * Time: 上午10:55
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userActivityList(Request $request)
    {
        $user_id = $request->input('user_id', null);
        $mobile = $request->input('mobile', null);
        $prize_id = $request->input('prize_id', 0);
        $state = $request->input('state', '3');


        $activity_id = $request->input('activity_id', 10001);
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

    /**
     * 导出中奖用户信息管理
     * User: zhouyao
     * Date: 2018/5/24
     * Time: 下午1:23
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $activity_id = $request->input('activity_id', 10001);
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

        unset($prize);

        //获取参与抽奖的用户数据
        $user = $DB->table('vault_user_prize_log as up')
            ->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
            ->where('up.activity_id', '=', $activity_id)
            ->where('up.type', '=', 0)
            ->select([
                'up.user_id',
                'up.prize_id',
                'up.state',
                'u.mobile',
                'u.user_name',
                'u.real_name',

            ])->get();
        $user = json_decode(json_encode($user), true);
        $user_id_arr = array_unique(array_column($user, 'user_id'));

        //获取用户投资总金额
        $user_invest = $DB->table('vault_user_invest_trade_log')
            ->whereIn('user_id', $user_id_arr)
            ->where('type', '=', 1)
            ->where('deal_term', '>=', 3)
            ->groupBy('user_id')
            ->pluck(DB::raw('sum(in_money * deal_term / 12) as in_money'), 'user_id');
        $user_invest = json_decode(json_encode($user_invest), true);


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
            $user_new[$val['user_id']]['total'] = isset($user_invest[$val['user_id']]) ? number_format($user_invest[$val['user_id']] / 100, 2) : 0;
            $user_new[$val['user_id']]['phone'] = isset($user_address_new[$val['user_id']]['phone']) ? $user_address_new[$val['user_id']]['phone'] : '';
            $user_new[$val['user_id']]['name'] = isset($user_address_new[$val['user_id']]['name'])? $user_address_new[$val['user_id']]['name'] : '';
            $user_new[$val['user_id']]['address'] = isset($user_address_new[$val['user_id']]['address']) ? $user_address_new[$val['user_id']]['address'] : '';
        }

        //单独求用户累计出借年金额
        $num = 1;
        foreach ($user_new as $key => &$val) {
            $val['num'] = $num;
            $num++;
        }

        //根据业务，自己进行模板赋值。
        $file_name   = "中奖用户名单-".date("Y-m-d H:i:s",time());
        $file_suffix = "xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file_name.$file_suffix");

        return view('activity.export', ['data' => $user_new]);
    }

    /*
     * 球队管理
     */
    public function footballTeam(Request $request) {
        $DB = DB::connection('vault');

        $result = $DB->table('vault_football_team')->get();
        $result = json_decode(json_encode($result), true);
        $this->view_data['data'] = $result;
        return view('activity.export', $this->view_data);
    }

}
