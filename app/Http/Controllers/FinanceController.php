<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Log;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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
        return view('finance.account-record', $this->view_data);
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

        $admin = $this->check_user();
        if($admin === true) {
            $user_start = 1;
            $user_end = 1000000;
        } else {
            $user_start = $admin;
            $user_end = $admin;
        }

        $total_count = DB::table('bill_record')->count();
        $statuses = [ 0=>'未上传',1=>'已传基准',2=>'已传实际',3=>'平账',4=>'不平账',];
        $lists = DB::table('bill_record')
            ->join('users', 'bill_record.user_id', '=', 'users.id')
            ->whereBetween('bill_record.user_id', [$user_start, $user_end])
            ->select(['bill_record.id','users.name','bill_record.business_identity','business_alias','bill_record.status','bill_record.created_at'])
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

    public function deleteAccountRecord(Request $request)
    {
        $id = $request->input('id');
        if($id <= 0) {
            return $this->error('删除失败');
        }
        
        $info = DB::table('bill_record')
                ->where('id',$id)
                ->first();
        if($info == false) {
            return $this->error('记录不存在');
        }

        //判断权限
        $admin = $this->check_user();
        if( $admin !== true ) {
            if( $info->user_id != $admin ) {
                return $this->error('无权操作他人数据');
            }
        }

        $result = DB::table('bill_record')
                ->where('id',$id)
                ->delete();
        if($result === false) {
            return $this->error('删除失败');
        }
        $result = DB::table('bill_detail_log')
                ->where('business_identity_id',$id)
                ->delete();
        if($result === false) {
            return $this->error('删除失败');
        }
        return $this->success('删除成功');
    }

    public function addAccountRecord(Request $request)
    {
        $this->view_data['meta_title'] = '业务对账记录';
        return view('finance.add-account-record', $this->view_data);
    }
    public function updateAccountRecord(Request $request)
    {
        $business_alias = $request->input('business_alias','');
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
            'business_alias' => $business_alias,
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

    public function accountRecordDetails(Request $request)
    {

        $id = $request->input('id');
        $info = DB::table('bill_record')
                ->where('id','=',$id)
                ->first();
        //判断权限
        $admin = $this->check_user();
        if( $admin !== true ) {
            if( $info->user_id != $admin ) {
                return parent::error('无权操作他人数据');
            }
        }
                
        $info = json_decode(json_encode($info),true);
        $statuses = [ 0=>'未上传',1=>'已传基准',2=>'已传实际',3=>'平账',4=>'不平账',];
        $overall_data = [];
        $overall_data[] = ['key' => '业务名称', 'value' => $info['business_alias'] ];
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

        $this->view_data['business_identity_id'] = $business_identity_id;

        $this->view_data['overall_data'] = $overall_data;
        $this->view_data['basic_data'] = $basic_data;
        $this->view_data['actual_data'] = $actual_data;

        //session
        $basic = [];
        $basic['header'] = session('basic.header','1');
        $basic['column_request_no'] = session('basic.column_request_no','A');
        $basic['column_amount'] = session('basic.column_amount','B');
        $basic['trim_string'] = session('basic.trim_string','');

        $actual = [];
        $actual['header'] = session('actual.header','1');
        $actual['column_request_no'] = session('actual.column_request_no','A');
        $actual['column_amount'] = session('actual.column_amount','B');
        $actual['trim_string'] = session('actual.trim_string','');

        $this->view_data['basic_config'] = $basic;
        $this->view_data['actual_config'] = $actual;

        return view('finance.account-record-details', $this->view_data);
    }

    public function uploadBasicData(Request $request)
    {
        if ($_FILES["file"]["error"] > 0)
        {
            return $this->error($_FILES["file"]["error"]);
        }

        $business_identity_id = $request->input('business_identity_id');

        $file_name = $_FILES["file"]["name"];
        $tmp_name = $_FILES["file"]["tmp_name"];

        if( $file_name == null ) {
            return $this->error('原文件名为空');
        }

        if( $tmp_name == null ) {
            return $this->error('临时文件不存在');
        }

        $header = $request->input('header');
        if($header == 1) {
            $start_row = 2;
        } else {
            $start_row = 1;
        }
        $column_request_no = $request->input('column_request_no','');
        if( $column_request_no == '') {
            $column_request_no = 'A';
        } else {
            $column_request_no = trim(strtoupper($column_request_no));
        }
        $column_amount = $request->input('column_amount','');
        if( $column_amount == '') {
            $column_amount = 'B';
        } else {
            $column_amount = trim(strtoupper($column_amount));
        }
        $trim_string = $request->input('trim_string','');
        $trim_string = trim($trim_string);

        session(['basic.header' => $header]);
        session(['basic.column_request_no' => $column_request_no]);
        session(['basic.column_amount' => $column_amount]);
        session(['basic.trim_string' => $trim_string]);

        set_time_limit(0);
        ini_set ('memory_limit', '512M');
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($tmp_name);

        $sheet        = $spreadsheet->getSheet(0); // 读取第一個工作表
        $total_row    = $sheet->getHighestRow(); // 取得总行数
        $total_column = $sheet->getHighestColumn(); // 取得总列数
        $result = ['row' => $total_row , 'column' => $total_column];

        //DB::beginTransaction();

        $count = 0;
        $insert_data_array = [];
        $update_data_array = [];
        $default_status = 2;

        for ($row = $start_row; $row <= $total_row; $row++) //行号从1开始
        {
            $request_no = $sheet->getCell($column_request_no . $row)->getValue();
            $amount = $sheet->getCell($column_amount . $row)->getValue();
            if($trim_string == '') {
                $request_no = trim($request_no);
            } else {
                $request_no = trim($request_no,$trim_string." \r\n");
            }
            if($request_no == null){
	           continue;
            }
            if($amount == 0){
                $amount = 0;
            } else{
                $amount = floatval($amount);
            }
            $bill_info = DB::table('bill_detail_log')
                    ->where('request_no','=',$request_no)
                    ->where('business_identity_id','=',$business_identity_id)
                    ->first();

            if($bill_info == null ) {
                $insert_data_array["{$request_no}---{$business_identity_id}"] = [ 'business_identity_id' => $business_identity_id,'request_no' => $request_no,'amount' =>$amount,'status'=>$default_status];
            } else {
                if( $bill_info->account_amount == 0 ) {
                    $status = 2;
                } elseif ($bill_info->account_amount == $amount) {
                    $status = 1;
                } else {
                    $status = 4;
                }
                $update_data_array[$bill_info->id] = ['amount' => $amount,'status' => $status];

            }
            $count++;
            if( $count % 5000 == 0 ) {
                // DB::commit();
                // DB::beginTransaction();
                if($insert_data_array != null) {
                    $insert_data = '';
                    foreach ( $insert_data_array as $key => $value) {
                        $insert_data .= "('{$value['business_identity_id']}','{$value['request_no']}','{$value['amount']}','{$value['status']}'),";
                    }
                    $insert_data = trim($insert_data,',');
                    $sql = "insert into tools_bill_detail_log (`business_identity_id`,`request_no`,`base_amount`,`status`) values {$insert_data}";
                    //Log::error($sql);
                    $status = DB::statement($sql);
                    if( $status !== true ) {
                        return $this->error('上传失败1');
                    }
                }

                if($update_data_array != null) {
                    $when1 = '';
                    $when2 = '';
                    $ids = '';

                    foreach ($update_data_array as $key => $value) {
                        $ids .= "{$key},";
                        $when1 .= sprintf("WHEN %d THEN %f ", $key, $value['amount']);
                        $when2 .= sprintf("WHEN %d THEN %f ", $key, $value['status']);
                    }

                    $ids = trim($ids,',');

                    $sql = "UPDATE tools_bill_detail_log SET base_amount = CASE id {$when1} END , status = CASE id {$when2} END WHERE id IN ($ids)";
                    //Log::error($sql);
                    $status = DB::statement($sql);
                    if( $status !== true ) {
                        return $this->error('上传失败2');
                    }
                }
                $insert_data_array = [];
                $update_data_array = [];
            }

        }
        //处理数据
        if($insert_data_array != null) {
            $insert_data = '';
            foreach ( $insert_data_array as $key => $value) {
                $insert_data .= "('{$value['business_identity_id']}','{$value['request_no']}','{$value['amount']}','{$value['status']}'),";
            }
            $insert_data = trim($insert_data,',');
            $sql = "insert into tools_bill_detail_log (`business_identity_id`,`request_no`,`base_amount`,`status`) values {$insert_data}";
            //Log::error($sql);
            $status = DB::statement($sql);
            if( $status !== true ) {
                return $this->error('上传失败1');
            }
        }

        if($update_data_array != null) {
            $when1 = '';
            $when2 = '';
            $ids = '';

            foreach ($update_data_array as $key => $value) {
                $ids .= "{$key},";
                $when1 .= sprintf("WHEN %d THEN %f ", $key, $value['amount']);
                $when2 .= sprintf("WHEN %d THEN %f ", $key, $value['status']);
            }

            $ids = trim($ids,',');

            $sql = "UPDATE tools_bill_detail_log SET base_amount = CASE id {$when1} END , status = CASE id {$when2} END WHERE id IN ($ids)";
            $status = DB::statement($sql);
            if( $status !== true ) {
                return $this->error('上传失败2');
            }
        }

        //DB::commit();

        //更新对账状态
        $record_info = DB::table('bill_record')
                        ->where('id','=',$business_identity_id)
                        ->first();
        if($record_info->status == 0) {
            $bill_status = 1;
        } elseif($record_info->status == 2 || $record_info->status == 3 || $record_info->status == 4) {
            $error_num = DB::table('bill_detail_log')
                ->where('business_identity_id','=',$business_identity_id)
                ->where('status','!=',1)
                ->count();
            if($error_num == 0) {
                $bill_status = 3;
            } else {
                $bill_status = 4;
            }
            
        }
        if(isset($bill_status)) {
            DB::table('bill_record')
                ->where('id','=',$business_identity_id)
                ->update(
                        [
                            'status' => $bill_status,
                        ]
                    );
        }

        return $this->success("{$count}条数据");
    }

    public function uploadActualData(Request $request)
    {
        if ($_FILES["file"]["error"] > 0)
        {
            return $this->error($_FILES["file"]["error"]);
        }

        $business_identity_id = $request->input('business_identity_id');

        $file_name = $_FILES["file"]["name"];
        $tmp_name = $_FILES["file"]["tmp_name"];

        if( $file_name == null ) {
            return $this->error('原文件名为空');
        }

        if( $tmp_name == null ) {
            return $this->error('临时文件不存在');
        }

        $header = $request->input('header');
        if($header == 1) {
            $start_row = 2;
        } else {
            $start_row = 1;
        }
        $column_request_no = $request->input('column_request_no','');
        if( $column_request_no == '') {
            $column_request_no = 'A';
        } else {
            $column_request_no = trim(strtoupper($column_request_no));
        }
        $column_amount = $request->input('column_amount','');
        if( $column_amount == '') {
            $column_amount = 'B';
        } else {
            $column_amount = trim(strtoupper($column_amount));
        }
        $trim_string = $request->input('trim_string','');
        $trim_string = trim($trim_string);

        session(['actual.header' => $header]);
        session(['actual.column_request_no' => $column_request_no]);
        session(['actual.column_amount' => $column_amount]);
        session(['actual.trim_string' => $trim_string]);

        set_time_limit(0);
        ini_set ('memory_limit', '512M');
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($tmp_name);

        $sheet        = $spreadsheet->getSheet(0); // 读取第一個工作表
        $total_row    = $sheet->getHighestRow(); // 取得总行数
        $total_column = $sheet->getHighestColumn(); // 取得总列数
        $result = ['row' => $total_row , 'column' => $total_column];

        $reader = new Xlsx();
        $spreadsheet = $reader->load($tmp_name);

        $sheet        = $spreadsheet->getSheet(0); // 读取第一個工作表
        $total_row    = $sheet->getHighestRow(); // 取得总行数
        $total_column = $sheet->getHighestColumn(); // 取得总列数
        $result = ['row' => $total_row , 'column' => $total_column];

        //DB::beginTransaction();

        $count = 0;
        $insert_data_array = [];
        $update_data_array = [];
        $default_status = 3;

        for ($row = $start_row; $row <= $total_row; $row++) //行号从1开始
        {
            $request_no = $sheet->getCell($column_request_no . $row)->getValue();
            $amount = $sheet->getCell($column_amount . $row)->getValue();
            if($trim_string == '') {
                $request_no = trim($request_no);
            } else {
                $request_no = trim($request_no,$trim_string." \r\n");
            }
	    if($request_no == null){
	        continue;
            }
            if($amount == 0){
                $amount = 0;
            } else {
                $amount = floatval($amount);

            }
        
            $bill_info = DB::table('bill_detail_log')
                    ->where('request_no','=',$request_no)
                    ->where('business_identity_id','=',$business_identity_id)
                    ->first();
            if($bill_info == null ) {
                $insert_data_array["{$request_no}---{$business_identity_id}"] = [ 'business_identity_id' => $business_identity_id,'request_no' => $request_no,'amount' =>$amount,'status'=>$default_status];
            } else {
                if( $bill_info->base_amount == 0 ) {
                    $status = 3;
                } elseif ($bill_info->base_amount == $amount) {
                    $status = 1;
                } else {
                    $status = 4;
                }
                $update_data_array[$bill_info->id] = ['amount' => $amount,'status' => $status];

            }

            $count++;
            if( $count % 5000 == 0 ) {
                //DB::commit();
                //DB::beginTransaction();
                if($insert_data_array != null) {
                    $insert_data = '';
                    foreach ( $insert_data_array as $key => $value) {
                        $insert_data .= "('{$value['business_identity_id']}','{$value['request_no']}','{$value['amount']}','{$value['status']}'),";
                    }
                    $insert_data = trim($insert_data,',');
                    $sql = "insert into tools_bill_detail_log (`business_identity_id`,`request_no`,`account_amount`,`status`) values {$insert_data}";
                    //Log::error($sql);
                    $status = DB::statement($sql);
                    if( $status !== true ) {
                        return $this->error('上传失败1');
                    }
                }

                if($update_data_array != null) {
                    $when1 = '';
                    $when2 = '';
                    $ids = '';

                    foreach ($update_data_array as $key => $value) {
                        $ids .= "{$key},";
                        $when1 .= sprintf("WHEN %d THEN %f ", $key, $value['amount']);
                        $when2 .= sprintf("WHEN %d THEN %f ", $key, $value['status']);
                    }

                    $ids = trim($ids,',');

                    $sql = "UPDATE tools_bill_detail_log SET account_amount = CASE id {$when1} END , status = CASE id {$when2} END WHERE id IN ($ids)";
                    //Log::error($sql);
                    $status = DB::statement($sql);
                    if( $status !== true ) {
                        return $this->error('上传失败2');
                    }
                }
                $insert_data_array = [];
                $update_data_array = [];

            }
        }
        //DB::commit();
        if($insert_data_array != null) {
            $insert_data = '';
            foreach ( $insert_data_array as $key => $value) {
                $insert_data .= "('{$value['business_identity_id']}','{$value['request_no']}','{$value['amount']}','{$value['status']}'),";
            }
            $insert_data = trim($insert_data,',');
            $sql = "insert into tools_bill_detail_log (`business_identity_id`,`request_no`,`account_amount`,`status`) values {$insert_data}";
            //Log::error($sql);
            $status = DB::statement($sql);
            if( $status !== true ) {
                return $this->error('上传失败1');
            }
        }

        if($update_data_array != null) {
            $when1 = '';
            $when2 = '';
            $ids = '';

            foreach ($update_data_array as $key => $value) {
                $ids .= "{$key},";
                $when1 .= sprintf("WHEN %d THEN %f ", $key, $value['amount']);
                $when2 .= sprintf("WHEN %d THEN %f ", $key, $value['status']);
            }

            $ids = trim($ids,',');

            $sql = "UPDATE tools_bill_detail_log SET account_amount = CASE id {$when1} END , status = CASE id {$when2} END WHERE id IN ($ids)";
            //Log::error($sql);
            $status = DB::statement($sql);
            if( $status !== true ) {
                return $this->error('上传失败2');
            }
        }
        //更新对账状态
        $record_info = DB::table('bill_record')
                        ->where('id','=',$business_identity_id)
                        ->first();
        if($record_info->status == 0) {
            $bill_status = 2;
        } elseif($record_info->status == 1 || $record_info->status == 3 || $record_info->status == 4) {
            $error_num = DB::table('bill_detail_log')
                ->where('business_identity_id','=',$business_identity_id)
                ->where('status','!=',1)
                ->count();
            if($error_num == 0) {
                $bill_status = 3;
            } else {
                $bill_status = 4;
            }
        }
        if(isset($bill_status)) {
            DB::table('bill_record')
                ->where('id','=',$business_identity_id)
                ->update(
                        [
                            'status' => $bill_status,
                        ]
                    );
        }

        //$result['data'] = $data;

        //$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        return $this->success('处理成功');
    }

    public function showData(Request $request)
    {
        $business_identity_id = $request->input('id');
        $type = $request->input('type');

        if($type == 0) {
            $type = 0;
        }

        $types = [ 0=>'全部记录' , 1 => '错误记录'];

        $record_info = DB::table('bill_record')
                            ->where('id','=',$business_identity_id)
                            ->first();

        $this->view_data['meta_title'] = $record_info->business_alias.'-'.$record_info->business_identity.'-'.$types[$type];
        $this->view_data['type'] = $type;
        $this->view_data['business_identity_id'] = $business_identity_id;
        return view('finance.show-data', $this->view_data);
    }

    public function getData(Request $request)
    {
        $business_identity_id = $request->input('business_identity_id');
        $type = $request->input('type');
        if($type == 0 ) {
            $status = [1,2,3,4];
        } elseif($type == 1 ) {
            $status = [2,3,4];
        }
        $page = $request->input('page');
        $limit = $request->input('limit');
        if($page <= 1) {
            $page = 1;
        }
        if($limit <= 1) {
            $limit = 10;
        }

        $limit_start = ($page - 1) * $limit;

        $total_count = DB::table('bill_detail_log')
                            ->where('business_identity_id','=',$business_identity_id)
                            ->whereIn('status',$status)
                            ->count();
        $statuses = [ 1=>'平账',2=>'长款',3=>'短款',4=>'存疑'];
        $lists = DB::table('bill_detail_log')
                    ->where('business_identity_id','=',$business_identity_id)
                    ->whereIn('status',$status)
                    ->select(['request_no','base_amount','account_amount','status'])
                    ->orderBy('id', 'asc')
                    ->offset($limit_start)
                    ->limit($limit)
                    ->get();
        $lists = json_decode(json_encode($lists),true);
        $count = $limit_start + 1;
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

    public function exportData(Request $request)
    {
        $xls_data = [];
        $xls_data[] = ['序号','流水号','基准金额','实际金额','对账状态'];
        try{

            $business_identity_id = $request->input('business_identity_id');
            $type = $request->input('type');
            if($type == 0 ) {
                $status = [1,2,3,4];
            } elseif($type == 1 ) {
                $status = [2,3,4];
            }

            $record_info = DB::table('bill_record')
                                ->where('id','=',$business_identity_id)
                                ->first();
            $file_name = $record_info->business_alias.'-'.$record_info->business_identity.".xlsx";
            $statuses = [ 1=>'平账',2=>'长款',3=>'短款',4=>'存疑'];
            $lists = DB::table('bill_detail_log')
                        ->where('business_identity_id','=',$business_identity_id)
                        ->whereIn('status',$status)
                        ->select(['request_no','base_amount','account_amount','status'])
                        ->orderBy('id', 'asc')
                        // ->offset($limit_start)
                        // ->limit($limit)
                        ->get();
            $lists = json_decode(json_encode($lists),true);

            $xls_data = [];
            $xls_data[] = ['序号','流水号','基准金额','实际金额','对账状态'];
            $count = 1;
            foreach ($lists as $key=>$value) {
                $tmp_data = [];
                $tmp_data[] = $count;
                $tmp_data[] = $value['request_no'] ;
                $tmp_data[] = $value['base_amount'] ;
                $tmp_data[] = $value['account_amount'] ;
                $tmp_data[] = $statuses[$value['status']];
                $count++;
                $xls_data[] = $tmp_data;
            }
        } catch(\Exception $e) {

        } finally{
            ini_set('memory_limit', '1280M');
            // $file_name = $business_identity_id.".xlsx";
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
            $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

            $phpexcel->getActiveSheet()->fromArray($xls_data,null,'A1',true);
            $phpexcel->getActiveSheet()->setTitle($sheet);
            $row = count($xls_data);
            //$phpexcel->getActiveSheet()->getStyle("A1:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            //$phpexcel->getActiveSheet()->getStyle("A1:E1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
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

    /**
     * 判断当前用户ID
     */
    private function check_user(){
        $user_id = Auth::id();

        if ($user_id != 1) {
            return $user_id;
        } else {
            return true;
        }
    }

}
