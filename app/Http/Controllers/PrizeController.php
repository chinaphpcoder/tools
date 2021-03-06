<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class PrizeController extends Controller
{

    public function manage(Request $request)
    {
        //判断权限
        $check = $this->check_user();
        if ($check !== true) {
            $admin = 0;
        } else {
            $admin = 1;
        }

        $identification = $request->route('identification','');
        $is_test = $request->input('is_test', '0');
        $this->view_data['is_test'] = $is_test;
        $this->view_data['meta_title'] = '活动奖品管理';
        $this->view_data['identification'] = $identification;
        $this->view_data['admin'] = $admin;

        return view('prize.manage', $this->view_data);
    }

    public function prize_list(Request $request)
    {
        $identification = $request->route('identification','');
        $is_test = $request->input('is_test', '0');
        $lists = DB::connection('vault')
                    ->table('vault_activity_prize')
                    ->join('vault_activity', 'vault_activity_prize.activity_id', '=', 'vault_activity.id')
                    ->where('vault_activity.activity_identification','=',$identification)
                    ->where('vault_activity.is_test','=',$is_test)
                    ->select(['vault_activity_prize.id','vault_activity_prize.admin_prize_name','vault_activity_prize.obtain_probability','vault_activity.is_test'])
                    ->get();
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

    public function probability_edit(Request $request)
    {
        $id = $request->input('id');
        $info = DB::connection('vault')->table('vault_activity_prize')->where('id','=',$id)->first();
        $this->view_data['obtain_probability'] = $info->obtain_probability;
        $this->view_data['id'] = $id;
        return view('prize.probability_edit', $this->view_data);
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

    public function details(Request $request)
    {
        $id = $request->input('id');
        $info = DB::connection('vault')->table('vault_activity_prize')->where('id','=',$id)->first();
        $conditions = $info->obtain_conditions;
        $conditions = json_decode($conditions,true);

        $obtain_conditions = [];

        $obtain_conditions['obtain_limit']['key'] = '奖品上限';
        $obtain_conditions['obtain_limit']['value'] = isset($conditions['obtain_limit']) ? $conditions['obtain_limit'] : '无限制';
        $obtain_conditions['start_times']['key'] = '最少抽奖次数';
        $obtain_conditions['start_times']['value'] = isset($conditions['start_times']) ? $conditions['start_times'] : '1';

        $attribute = $info->prize_attribute;
        $attribute = json_decode($attribute,true);

        $prize_attribute = [];
        $prize_attribute['red_packet_name']['key'] = '红包名称';
        $prize_attribute['red_packet_name']['value'] = isset($attribute['red_packet_name']) ? $attribute['red_packet_name'] : '';
        $prize_attribute['red_packet_amount']['key'] = '红包金额';
        $prize_attribute['red_packet_amount']['value'] = isset($attribute['red_packet_amount']) ? $attribute['red_packet_amount'] : '';
        $prize_attribute['invest_term_type']['key'] = '期限类型';
        $prize_attribute['invest_term_type']['value'] = isset($attribute['invest_term_type']) ? $attribute['invest_term_type'] : '';
        $prize_attribute['invest_term_number']['key'] = '期限长度';
        $prize_attribute['invest_term_number']['value'] = isset($attribute['invest_term_number']) ? $attribute['invest_term_number'] : '';
        $prize_attribute['invest_amount']['key'] = '投资金额';
        $prize_attribute['invest_amount']['value'] = isset($attribute['invest_amount']) ? $attribute['invest_amount'] : '';
        $prize_attribute['invest_products']['key'] = '适用产品';
        $prize_attribute['invest_products']['value'] = isset($attribute['invest_products']) ? $attribute['invest_products'] : '';
        $prize_attribute['type']['key'] = '红包类型';
        $prize_attribute['type']['value'] = isset($attribute['type']) ? $attribute['type'] : '';
        $prize_attribute['end_time']['key'] = '红包有效期';
        $prize_attribute['end_time']['value'] = isset($attribute['end_time']) ? $attribute['end_time'] : '';

        $this->view_data['obtain_conditions'] = $obtain_conditions;
        $this->view_data['type'] = $info->type;
        $this->view_data['prize_attribute'] = $prize_attribute;

        return view('prize.details', $this->view_data);
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
