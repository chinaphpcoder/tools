<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class FinanceController extends Controller
{

    public function accountRecord(Request $request)
    {
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            $admin = 0;
        } else {
            $admin = 1;
        }

        $this->view_data['meta_title'] = '业务对账记录';
        $this->view_data['admin'] = $admin;
        return view('finance.accountRecord', $this->view_data);
    }

    public function getAccountRecord(Request $request)
    {
        $page = $request->input('page');
        $limit = $request->input('limit');
        if($page <= 1) {
            $page = 1;
        }
        if($limit <= 1) {
            $limit = 10;
        }

        $limit_start = ($page - 1) * $limit;

        $total_count = DB::table('bill_record')->count();
        $statuses = [ 0=>'未上传数据'];
        $lists = DB::table('bill_record')
            ->join('users', 'bill_record.user_id', '=', 'users.id')
            ->select(['bill_record.id','users.name','bill_record.business_identity','bill_record.status','bill_record.created_at'])
            ->orderBy('bill_record.id', 'desc')
            ->offset($limit_start)
            ->limit($limit)
            ->get();
        $lists = json_decode(json_encode($lists),true);
        $count = 1;
        foreach ($lists as $key=>$value) {
            $lists[$key]['pid'] = $count;
            $lists[$key]['status_text'] = $statuses[$value['status']];
            $count++;
        }
        $result = [];
        $result['code'] = 0;
        $result['msg'] = '';
        $result['count'] = $total_count;
        $result['data'] = $lists;
        return $result;
    }

    public function addAccountRecord(Request $request)
    {
        $ymd = date("Ymd");
        $pre_business_identity = DB::table('bill_record')
                    ->where('business_identity','like',"{$ymd}%")
                    ->max('business_identity');
        
        if($pre_business_identity == null) {
            $pre_business_identity = date("Ymd")."0000";
        }
        $business_identity = $pre_business_identity + 1;
        
        $insert_data = [
            'business_identity' => $business_identity,
            'status' => 0,
            'user_id' => Auth::id(),
        ];

        $id = DB::table('bill_record')
                ->insert($insert_data);
        if ( $id <= 0 ) {
            return $this->error('添加失败');
        }
        return $this->success('添加成功');
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

    public function accountRecordDetails(Request $request)
    {
        $id = $request->input('id');
        $info = DB::table('bill_record')
                ->where('id','=',$id)
                ->first();
        $info = json_decode(json_encode($info),true);
        $statuses = [ 0=>'未上传数据'];
        $overall_data = [];
        $overall_data[] = ['key' => '业务标识', 'value' => $info['business_identity'] ];
        $overall_data[] = ['key' => '总体进度', 'value' => $statuses[$info['status']] ];
        $overall_data[] = ['key' => '创建时间', 'value' => $info['created_at'] ];
        $business_identity_id = $info['id'];
        $base_info = DB::table('bill_detail_log')
                ->where('business_identity_id','=',$business_identity_id)
                ->where('base_amount','>',0)
                ->select(
                    DB::raw('count(*) as count'),
                    DB::raw('sum(base_amount) as total_amount')
                )
                ->get();
        $base_info = json_decode(json_encode($base_info),true);
        $base_info = $base_info[0];

        $basic_data = [];
        $basic_data[] = ['key' => '数据条数', 'value' => $base_info['count'] == null ? 0 : $base_info['count'] ];
        $basic_data[] = ['key' => '总金额(元)', 'value' => $base_info['total_amount'] == null ? 0 : $base_info['total_amount'] ];

        $actual_info = DB::table('bill_detail_log')
                ->where('business_identity_id','=',$business_identity_id)
                ->where('account_amount','>',0)
                ->select(
                    DB::raw('count(*) as count'),
                    DB::raw('sum(account_amount) as total_amount')
                )
                ->get();
        $actual_info = json_decode(json_encode($actual_info),true);
        $actual_info = $actual_info[0];

        $actual_data = [];
        $actual_data[] = ['key' => '数据条数', 'value' => $actual_info['count'] == null ? 0 : $actual_info['count'] ];
        $actual_data[] = ['key' => '总金额(元)', 'value' => $actual_info['total_amount'] == null ? 0 : $actual_info['total_amount'] ];

        $this->view_data['overall_data'] = $overall_data;
        $this->view_data['basic_data'] = $basic_data;
        $this->view_data['actual_data'] = $actual_data;

        return view('finance.account-record-details', $this->view_data);
    }

    public function uploadBasicData(Request $request)
    {
        return $this->success('aaaa');
    }

    public function uploadActualData(Request $request)
    {
        return $this->success('bbbb');
    }

    public function winning_record(Request $request)
    {
        $identification = $request->route('identification','');
        
        $is_test = $request->input('is_test', null);

        $user_id = $request->input('user_id', null);
        $mobile = $request->input('mobile', null);
        $prize_id = $request->input('prize_id', 0);

        $conditions = [];
        $conditions['user_id'] = $user_id;
        $conditions['mobile'] = $mobile;
        $conditions['prize_id'] = $prize_id;
        if( $is_test !== null )
        {
            $conditions['is_test'] = $is_test;
        }

        $appends = [];
        if( $is_test === null ) {
            $is_test = 0; 
        } else {
            $appends['is_test'] = $is_test;
        }
        if($user_id != null) {
            $appends['user_id'] = $user_id;
        }
        if($mobile != null) {
            $appends['mobile'] = $mobile;
        }

        if($prize_id != null) {
            $appends['prize_id'] = $prize_id;
        }

        $activity_info = DB::connection('vault')
                    ->table('vault_activity')
                    ->where('vault_activity.activity_identification','=',$identification)
                    ->where('vault_activity.is_test','=',$is_test)
                    ->first();
        $activity_id = 0;
        if( isset($activity_info->id) ) {
            $activity_id = $activity_info->id;
        }
        //初始化数据
        $prize = [];
        $user_prize_list = [];
        $prize_list = null;

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
            $user_prize = DB::connection('vault')->table('vault_user_prize_log as up');

            //查询用户名对应的记录
            if (!empty($user_id)) {
                $search_user = DB::connection('vault')->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
                if( !isset($search_user[0]) ) {
                    $tmp_user_id = 0;
                } else {
                    $tmp_user_id = $search_user[0];
                }
                $user_prize->where('user_id', '=', $tmp_user_id);
            }

            //查询手机号对应的记录
            if (!empty($mobile)) {
                $search_mobile = DB::connection('vault')->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
                if( !isset($search_mobile[0]) ) {
                    $tmp_user_id = 0;
                } else {
                    $tmp_user_id = $search_mobile[0];
                }
                $user_prize->where('user_id', '=', $tmp_user_id);
            }

            if (!empty($prize_id)) {
                $user_prize->where('prize_id', '=', $prize_id);
            }
            $prize_list =$user_prize->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
            ->where('up.activity_id', '=', $activity_id)
            ->select([
                'up.user_id',
                'up.prize_id',
                'up.time',
                'up.state',
                'u.mobile',
                'u.user_name',
                'u.real_name',

            ])->paginate(20);
            
            $user_prize_list = json_decode(json_encode($prize_list), true);
            $prize_list->appends($appends);

            $num_start = $user_prize_list['from'];

            $user_prize_list = $user_prize_list['data'];
            if( $user_prize_list ) {
                $user_id_arr = array_unique(array_column($user_prize_list, 'user_id'));
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
                // 获取累计年出借额
                $invest_info = DB::connection('vault')->table('vault_activity_user_info')
                                ->where('activity_id', '=', $activity_id)
                                ->whereIn('user_id', $user_id_arr)
                                ->select(['total_invest_money','total_year_invest_money','total_draw_number','remain_draw_number','user_id'])
                                ->get();
                $invest_info = json_decode(json_encode($invest_info), true);
                $year_invest_info = [];
                foreach ($invest_info as $key => $value) {
                    $year_invest_info[$value['user_id']] = $value;
                }

                //根据用户取出奖品信息并去重用户数据
                $num = $num_start;
                foreach ($user_prize_list as $key => $value) {
                    $user_prize_list[$key]['num'] = $num++;
                    $user_prize_list[$key]['prize_name'] = isset($prize_new[$value['prize_id']]['prize_name']) ? $prize_new[$value['prize_id']]['prize_name'] : '';
                    $user_prize_list[$key]['total_invest_money'] = isset($year_invest_info[$value['user_id']]['total_invest_money']) ? $year_invest_info[$value['user_id']]['total_invest_money'] / 100 : '';
                    $user_prize_list[$key]['total_year_invest_money'] = isset($year_invest_info[$value['user_id']]['total_year_invest_money']) ? $year_invest_info[$value['user_id']]['total_year_invest_money'] / 100 : '';
                    $user_prize_list[$key]['total_draw_number'] = isset($year_invest_info[$value['user_id']]['total_draw_number']) ? $year_invest_info[$value['user_id']]['total_draw_number'] : '';
                    $user_prize_list[$key]['remain_draw_number'] = isset($year_invest_info[$value['user_id']]['remain_draw_number']) ? $year_invest_info[$value['user_id']]['remain_draw_number'] : '';
                    $user_prize_list[$key]['consignee_name'] = isset($user_address_new[$value['user_id']]['name'])? $user_address_new[$value['user_id']]['name'] : '';
                    $user_prize_list[$key]['consignee_mobile'] = isset($user_address_new[$value['user_id']]['phone'])? $user_address_new[$value['user_id']]['phone'] : '';
                    $user_prize_list[$key]['consignee_address'] = isset($user_address_new[$value['user_id']]['address'])? $user_address_new[$value['user_id']]['address'] : '';
                }

            }
            
        }

        $this->view_data['meta_title'] = '中奖用户管理';
        $this->view_data['identification'] = $identification;
        $this->view_data['prize'] = $prize;
        $this->view_data['user_prize_list'] = $user_prize_list;
        $this->view_data['page'] = $prize_list;
        $this->view_data['conditions'] = $conditions;
        return view('prize.winning_record', $this->view_data);
    }

    public function winning_record_export(Request $request)
    {
        // 表头字段
        $xls_data = [];
        $xls_data[] = ['序号','用户ID','姓名','手机号','累计出借金额','累计年出借金额','累计可抽奖次数','剩余抽奖次数','奖品','收货信息','',''];
        $xls_data[] = ['','','','','','','','','','姓名','电话','收货地址'];

        try{
            $identification = $request->route('identification','');

            $is_test = $request->input('is_test', null);

            $user_id = $request->input('user_id', null);
            $mobile = $request->input('mobile', null);
            $prize_id = $request->input('prize_id', 0);

            if( $is_test === null )
            {
                $is_test = 0;
            }

            $activity_info = DB::connection('vault')
                ->table('vault_activity')
                ->where('vault_activity.activity_identification','=',$identification)
                ->where('vault_activity.is_test','=',$is_test)
                ->first();

            $activity_id = 0;
            if( isset($activity_info->id) ) {
                $activity_id = $activity_info->id;
            }
            //初始化数据
            $prize = [];
            $user_prize_list = [];
            $prize_list = null;

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
                $user_prize = DB::connection('vault')->table('vault_user_prize_log as up');

                //查询用户名对应的记录
                if (!empty($user_id)) {
                    $search_user = DB::connection('vault')->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
                    if( !isset($search_user[0]) ) {
                        $tmp_user_id = 0;
                    } else {
                        $tmp_user_id = $search_user[0];
                    }
                    $user_prize->where('user_id', '=', $tmp_user_id);
                }

                //查询手机号对应的记录
                if (!empty($mobile)) {
                    $search_mobile = DB::connection('vault')->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
                    if( !isset($search_mobile[0]) ) {
                        $tmp_user_id = 0;
                    } else {
                        $tmp_user_id = $search_mobile[0];
                    }
                    $user_prize->where('user_id', '=', $tmp_user_id);
                }

                if (!empty($prize_id)) {
                    $user_prize->where('prize_id', '=', $prize_id);
                }
                $prize_list =$user_prize->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
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

                $user_prize_list = json_decode(json_encode($prize_list), true);

                $num_start = 1;

                if( $user_prize_list ) {
                    $user_id_arr = array_unique(array_column($user_prize_list, 'user_id'));
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

                    // 获取累计年出借额
                    $invest_info = DB::connection('vault')->table('vault_activity_user_info')
                                    ->where('activity_id', '=', $activity_id)
                                    ->whereIn('user_id', $user_id_arr)
                                    ->select(['total_invest_money','total_year_invest_money','total_draw_number','remain_draw_number','user_id'])
                                    ->get();
                    $invest_info = json_decode(json_encode($invest_info), true);
                    $year_invest_info = [];
                    foreach ($invest_info as $key => $value) {
                        $year_invest_info[$value['user_id']] = $value;
                    }

                    //根据用户取出奖品信息并去重用户数据
                    $num = $num_start;
                    foreach ($user_prize_list as $key => $value) {
                        $user_prize_list[$key]['num'] = $num++;
                        $user_prize_list[$key]['prize_name'] = isset($prize_new[$value['prize_id']]['prize_name']) ? $prize_new[$value['prize_id']]['prize_name'] : '';
                        $user_prize_list[$key]['total_invest_money'] = isset($year_invest_info[$value['user_id']]['total_invest_money']) ? $year_invest_info[$value['user_id']]['total_invest_money'] : '';
                        $user_prize_list[$key]['total_year_invest_money'] = isset($year_invest_info[$value['user_id']]['total_year_invest_money']) ? $year_invest_info[$value['user_id']]['total_year_invest_money'] : '';
                        $user_prize_list[$key]['total_draw_number'] = isset($year_invest_info[$value['user_id']]['total_draw_number']) ? $year_invest_info[$value['user_id']]['total_draw_number'] : '';
                        $user_prize_list[$key]['remain_draw_number'] = isset($year_invest_info[$value['user_id']]['remain_draw_number']) ? $year_invest_info[$value['user_id']]['remain_draw_number'] : '';
                        $user_prize_list[$key]['consignee_name'] = isset($user_address_new[$value['user_id']]['name'])? $user_address_new[$value['user_id']]['name'] : '';
                        $user_prize_list[$key]['consignee_mobile'] = isset($user_address_new[$value['user_id']]['phone'])? $user_address_new[$value['user_id']]['phone'] : '';
                        $user_prize_list[$key]['consignee_address'] = isset($user_address_new[$value['user_id']]['address'])? $user_address_new[$value['user_id']]['address'] : '';
                    }

                }

            }
            $xls_data = [];
            $xls_data[] = ['序号','用户ID','姓名','手机号','累计出借金额','累计年出借金额','累计可抽奖次数','剩余抽奖次数','奖品','收货信息','',''];
            $xls_data[] = ['','','','','','','','','','姓名','电话','收货地址'];

            foreach ($user_prize_list as $key=>$value) {
                $tmp_data = [];
                $tmp_data[] = $value['num'];
                $tmp_data[] = $value['user_id'] ;
                $tmp_data[] = $value['real_name'] ;
                $tmp_data[] = $value['mobile'] ;
                $tmp_data[] = $value['total_invest_money'] ;
                $tmp_data[] = $value['total_year_invest_money'] ;
                $tmp_data[] = $value['total_draw_number'] ;
                $tmp_data[] = $value['remain_draw_number'] ;
                $tmp_data[] = $value['prize_name'] ;
                $tmp_data[] = $value['consignee_name'] ;
                $tmp_data[] = $value['consignee_mobile'];
                $tmp_data[] = $value['consignee_address'] ;
                $xls_data[] = $tmp_data;
            }

        } catch (Exception $e){

        }finally{
            ini_set('memory_limit', '1280M');
            $file_name = "中奖用户名单-{$identification}-".date("YmdHis").".xlsx";
            $sheet = "Sheet1";
            $phpexcel = new PHPExcel();
            $phpexcel->getProperties()
                ->setCreator("shaxiaoseng php")
                ->setLastModifiedBy("shaxiaoseng php")
                ->setTitle("shaxiaoseng php document")
                ->setSubject("shaxiaoseng php document")
                ->setDescription("shaxiaoseng php")
                ->setKeywords("shaxiaoseng php")
                ->setCategory("shaxiaoseng php");
            $phpexcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $phpexcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
            $phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
            $phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $phpexcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $phpexcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $phpexcel->getActiveSheet()->getColumnDimension('L')->setWidth(50);

            $phpexcel->getActiveSheet()->fromArray($xls_data,null,'A1',true);
            $phpexcel->getActiveSheet()->setTitle($sheet);
            $phpexcel->getActiveSheet()->mergeCells('A1:A2');
            $phpexcel->getActiveSheet()->mergeCells('B1:B2');
            $phpexcel->getActiveSheet()->mergeCells('C1:C2');
            $phpexcel->getActiveSheet()->mergeCells('D1:D2');
            $phpexcel->getActiveSheet()->mergeCells('E1:E2');
            $phpexcel->getActiveSheet()->mergeCells('F1:F2');
            $phpexcel->getActiveSheet()->mergeCells('G1:G2');
            $phpexcel->getActiveSheet()->mergeCells('H1:H2');
            $phpexcel->getActiveSheet()->mergeCells('I1:I2');
            $phpexcel->getActiveSheet()->mergeCells('J1:L1');

            $phpexcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $row = count($xls_data);
            $phpexcel->getActiveSheet()->getStyle("A1:L{$row}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $phpexcel->getActiveSheet()->getStyle("A1:L2")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $phpexcel->setActiveSheetIndex(0);
            $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Cache-Control: max-age=0');
            $objwriter->save('php://output');
        }
    }

    public function lists(Request $request)
    {
        $identification = $request->route('identification','');
        
        $is_test = $request->input('is_test', null);

        $user_id = $request->input('user_id', null);
        $mobile = $request->input('mobile', null);
        $prize_id = $request->input('prize_id', 0);

        $conditions = [];
        $conditions['user_id'] = $user_id;
        $conditions['mobile'] = $mobile;
        $conditions['prize_id'] = $prize_id;
        if( $is_test !== null )
        {
            $conditions['is_test'] = $is_test;
        }

        $appends = [];
        if( $is_test === null ) {
            $is_test = 0; 
        } else {
            $appends['is_test'] = $is_test;
        }
        if($user_id != null) {
            $appends['user_id'] = $user_id;
        }
        if($mobile != null) {
            $appends['mobile'] = $mobile;
        }

        if($prize_id != null) {
            $appends['prize_id'] = $prize_id;
        }

        $activity_info = DB::connection('vault')
                    ->table('vault_activity')
                    ->where('vault_activity.activity_identification','=',$identification)
                    ->where('vault_activity.is_test','=',$is_test)
                    ->first();
        $activity_id = 0;
        if( isset($activity_info->id) ) {
            $activity_id = $activity_info->id;
        }
        //初始化数据
        $prize = [];
        $user_prize_list = [];
        $prize_list = null;

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
            $user_prize = DB::connection('vault')->table('vault_user_prize_log as up');

            //查询用户名对应的记录
            if (!empty($user_id)) {
                $search_user = DB::connection('vault')->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
                if( !isset($search_user[0]) ) {
                    $tmp_user_id = 0;
                } else {
                    $tmp_user_id = $search_user[0];
                }
                $user_prize->where('user_id', '=', $tmp_user_id);
            }

            //查询手机号对应的记录
            if (!empty($mobile)) {
                $search_mobile = DB::connection('vault')->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
                if( !isset($search_mobile[0]) ) {
                    $tmp_user_id = 0;
                } else {
                    $tmp_user_id = $search_mobile[0];
                }
                $user_prize->where('user_id', '=', $tmp_user_id);
            }

            if (!empty($prize_id)) {
                $user_prize->where('prize_id', '=', $prize_id);
            }
            $prize_list =$user_prize->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
            ->where('up.activity_id', '=', $activity_id)
            ->select([
                'up.user_id',
                'up.prize_id',
                'up.time',
                'up.state',
                'u.mobile',
                'u.user_name',
                'u.real_name',

            ])->paginate(20);
            
            $user_prize_list = json_decode(json_encode($prize_list), true);
            $prize_list->appends($appends);

            $num_start = $user_prize_list['from'];

            $user_prize_list = $user_prize_list['data'];
            if( $user_prize_list ) {
                $user_id_arr = array_unique(array_column($user_prize_list, 'user_id'));
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
                // 获取累计年出借额
                $invest_info = DB::connection('vault')->table('vault_activity_gold')
                                ->where('activity_id', '=', $activity_id)
                                ->whereIn('user_id', $user_id_arr)
                                ->select(['allgold as invest_money','user_id'])
                                ->get();
                $invest_info = json_decode(json_encode($invest_info), true);
                $year_invest_info = [];
                foreach ($invest_info as $key => $value) {
                    $year_invest_info[$value['user_id']] = $value;
                }

                //根据用户取出奖品信息并去重用户数据
                $num = $num_start;
                foreach ($user_prize_list as $key => $value) {
                    $user_prize_list[$key]['num'] = $num++;
                    $user_prize_list[$key]['prize_name'] = isset($prize_new[$value['prize_id']]['prize_name']) ? $prize_new[$value['prize_id']]['prize_name'] : '';
                    $user_prize_list[$key]['invest_money'] = isset($year_invest_info[$value['user_id']]['invest_money']) ? $year_invest_info[$value['user_id']]['invest_money'] : '';
                    $user_prize_list[$key]['consignee_name'] = isset($user_address_new[$value['user_id']]['name'])? $user_address_new[$value['user_id']]['name'] : '';
                    $user_prize_list[$key]['consignee_mobile'] = isset($user_address_new[$value['user_id']]['phone'])? $user_address_new[$value['user_id']]['phone'] : '';
                    $user_prize_list[$key]['consignee_address'] = isset($user_address_new[$value['user_id']]['address'])? $user_address_new[$value['user_id']]['address'] : '';
                }

            }
            
        }

        $this->view_data['meta_title'] = '中奖用户管理';
        $this->view_data['identification'] = $identification;
        $this->view_data['prize'] = $prize;
        $this->view_data['user_prize_list'] = $user_prize_list;
        $this->view_data['page'] = $prize_list;
        $this->view_data['conditions'] = $conditions;
        return view('prize.lists', $this->view_data);
    }


    public function export(Request $request)
    {
        // 表头字段
        $xls_data = [];
        $xls_data[] = ['序号','用户ID','姓名','手机号','累计年出借金额','奖品','收货信息','',''];
        $xls_data[] = ['','','','','','','姓名','电话','收货地址'];

        try{
            $identification = $request->route('identification','');

            $is_test = $request->input('is_test', null);

            $user_id = $request->input('user_id', null);
            $mobile = $request->input('mobile', null);
            $prize_id = $request->input('prize_id', 0);

            if( $is_test === null )
            {
                $is_test = 0;
            }

            $activity_info = DB::connection('vault')
                ->table('vault_activity')
                ->where('vault_activity.activity_identification','=',$identification)
                ->where('vault_activity.is_test','=',$is_test)
                ->first();

            $activity_id = 0;
            if( isset($activity_info->id) ) {
                $activity_id = $activity_info->id;
            }
            //初始化数据
            $prize = [];
            $user_prize_list = [];
            $prize_list = null;

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
                $user_prize = DB::connection('vault')->table('vault_user_prize_log as up');

                //查询用户名对应的记录
                if (!empty($user_id)) {
                    $search_user = DB::connection('vault')->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
                    if( !isset($search_user[0]) ) {
                        $tmp_user_id = 0;
                    } else {
                        $tmp_user_id = $search_user[0];
                    }
                    $user_prize->where('user_id', '=', $tmp_user_id);
                }

                //查询手机号对应的记录
                if (!empty($mobile)) {
                    $search_mobile = DB::connection('vault')->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
                    if( !isset($search_mobile[0]) ) {
                        $tmp_user_id = 0;
                    } else {
                        $tmp_user_id = $search_mobile[0];
                    }
                    $user_prize->where('user_id', '=', $tmp_user_id);
                }

                if (!empty($prize_id)) {
                    $user_prize->where('prize_id', '=', $prize_id);
                }
                $prize_list =$user_prize->leftJoin('vault_user as u', 'up.user_id', '=', 'u.id')
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

                $user_prize_list = json_decode(json_encode($prize_list), true);

                $num_start = 1;

                if( $user_prize_list ) {
                    $user_id_arr = array_unique(array_column($user_prize_list, 'user_id'));
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
                    // 获取累计年出借额
                    $invest_info = DB::connection('vault')->table('vault_activity_gold')
                        ->where('activity_id', '=', $activity_id)
                        ->whereIn('user_id', $user_id_arr)
                        ->select(['allgold as invest_money','user_id'])
                        ->get();
                    $invest_info = json_decode(json_encode($invest_info), true);
                    $year_invest_info = [];
                    foreach ($invest_info as $key => $value) {
                        $year_invest_info[$value['user_id']] = $value;
                    }

                    //根据用户取出奖品信息并去重用户数据
                    $num = $num_start;
                    foreach ($user_prize_list as $key => $value) {
                        $user_prize_list[$key]['num'] = $num++;
                        $user_prize_list[$key]['prize_name'] = isset($prize_new[$value['prize_id']]['prize_name']) ? $prize_new[$value['prize_id']]['prize_name'] : '';
                        $user_prize_list[$key]['invest_money'] = isset($year_invest_info[$value['user_id']]['invest_money']) ? $year_invest_info[$value['user_id']]['invest_money'] : '';
                        $user_prize_list[$key]['consignee_name'] = isset($user_address_new[$value['user_id']]['name'])? $user_address_new[$value['user_id']]['name'] : '';
                        $user_prize_list[$key]['consignee_mobile'] = isset($user_address_new[$value['user_id']]['phone'])? $user_address_new[$value['user_id']]['phone'] : '';
                        $user_prize_list[$key]['consignee_address'] = isset($user_address_new[$value['user_id']]['address'])? $user_address_new[$value['user_id']]['address'] : '';
                    }

                }

            }


            $xls_data = [];
            $xls_data[] = ['序号','用户ID','姓名','手机号','累计年出借金额','奖品','收货信息','',''];
            $xls_data[] = ['','','','','','','姓名','电话','收货地址'];

            foreach ($user_prize_list as $key=>$value) {
                $tmp_data = [];
                $tmp_data[] = $value['num'];
                $tmp_data[] = $value['user_id'] ;
                $tmp_data[] = $value['real_name'] ;
                $tmp_data[] = $value['mobile'] ;
                $tmp_data[] = $value['invest_money'] ;
                $tmp_data[] = $value['prize_name'] ;
                $tmp_data[] = $value['consignee_name'] ;
                $tmp_data[] = $value['consignee_mobile'];
                $tmp_data[] = $value['consignee_address'] ;
                $xls_data[] = $tmp_data;
            }

        } catch (Exception $e){

        }finally{
            ini_set('memory_limit', '1280M');
            $file_name = "中奖用户名单-{$identification}-".date("YmdHis").".xlsx";
            $sheet = "Sheet1";
            $phpexcel = new PHPExcel();
            $phpexcel->getProperties()
                ->setCreator("shaxiaoseng php")
                ->setLastModifiedBy("shaxiaoseng php")
                ->setTitle("shaxiaoseng php document")
                ->setSubject("shaxiaoseng php document")
                ->setDescription("shaxiaoseng php")
                ->setKeywords("shaxiaoseng php")
                ->setCategory("shaxiaoseng php");
            $phpexcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $phpexcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $phpexcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);

            $phpexcel->getActiveSheet()->fromArray($xls_data,null,'A1',true);
            $phpexcel->getActiveSheet()->setTitle($sheet);
            $phpexcel->getActiveSheet()->mergeCells('A1:A2');
            $phpexcel->getActiveSheet()->mergeCells('B1:B2');
            $phpexcel->getActiveSheet()->mergeCells('C1:C2');
            $phpexcel->getActiveSheet()->mergeCells('D1:D2');
            $phpexcel->getActiveSheet()->mergeCells('E1:E2');
            $phpexcel->getActiveSheet()->mergeCells('F1:F2');
            $phpexcel->getActiveSheet()->mergeCells('G1:I1');

            $phpexcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $row = count($xls_data);
            $phpexcel->getActiveSheet()->getStyle("A1:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $phpexcel->getActiveSheet()->getStyle("A1:I2")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $phpexcel->setActiveSheetIndex(0);
            $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Cache-Control: max-age=0');
            $objwriter->save('php://output');
        }
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

    private function data_to_xls($data,$filename='simple.xlsx',$sheet='Sheet1'){
        ini_set('memory_limit', '1280M');
        $filename = preg_replace('/\.xls[x]?/i','',$filename).".xlsx";
        $phpexcel = new \PHPExcel();
        $phpexcel->getProperties()
            ->setCreator("shaxiaoseng php")
            ->setLastModifiedBy("shaxiaoseng php")
            ->setTitle("shaxiaoseng php document")
            ->setSubject("shaxiaoseng php document")
            ->setDescription("shaxiaoseng php")
            ->setKeywords("shaxiaoseng php")
            ->setCategory("shaxiaoseng php");
        $phpexcel->getActiveSheet()->fromArray($data,null,'A1',true);
        $phpexcel->getActiveSheet()->setTitle($sheet);
        $phpexcel->setActiveSheetIndex(0);
        $objwriter = \PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
        $objwriter->save($filename);
    }

    /**
     * 判断当前用户ID
     */
    private function check_user(){
        $user_id = Auth::id();

        if ($user_id != 1) {
            return false;
        } else {
            return true;
        }
    }

}
