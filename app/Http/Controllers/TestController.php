<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\NewMenu;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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

    public function readxls() {

        $time_start = time();
        echo "start:",time(),"<br>";

        $tmp_name = "/tmp/123.xlsx";


        $business_identity_id = 1;

        $header = 1;
        if($header == 1) {
            $start_row = 2;
        } else {
            $start_row = 1;
        }
        $column_request_no = 'A';
        if( $column_request_no == '') {
            $column_request_no = 'A';
        } else {
            $column_request_no = trim(strtoupper($column_request_no));
        }
        $column_amount = 'B';
        if( $column_amount == '') {
            $column_amount = 'B';
        } else {
            $column_amount = trim(strtoupper($column_amount));
        }
        $trim_string = '';
        $trim_string = trim($trim_string);

        session(['basic.header' => $header]);
        session(['basic.column_request_no' => $column_request_no]);
        session(['basic.column_amount' => $column_amount]);
        session(['basic.trim_string' => $trim_string]);

        set_time_limit(0);
        ini_set ('memory_limit', '1024M');
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($tmp_name);

        $sheet        = $spreadsheet->getSheet(0); // 读取第一個工作表
        $total_row    = $sheet->getHighestRow(); // 取得总行数
        $total_column = $sheet->getHighestColumn(); // 取得总列数
        $result = ['row' => $total_row , 'column' => $total_column];

        //DB::beginTransaction();
        $default_db = config("database.default");
        $table_prefix = config("database.connections.{$default_db}.prefix");

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
                echo $count,":",time(),"<br>";
                // DB::commit();
                // DB::beginTransaction();
                if($insert_data_array != null) {
                    $insert_data = '';
                    foreach ( $insert_data_array as $key => $value) {
                        $insert_data .= "('{$value['business_identity_id']}','{$value['request_no']}','{$value['amount']}','{$value['status']}'),";
                    }
                    $insert_data = trim($insert_data,',');
                    $sql = "insert into {$table_prefix}bill_detail_log (`business_identity_id`,`request_no`,`base_amount`,`status`) values {$insert_data}";
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

                    $sql = "UPDATE {$table_prefix}bill_detail_log SET base_amount = CASE id {$when1} END , status = CASE id {$when2} END WHERE id IN ($ids)";
                    //Log::error($sql);
                    $status = DB::statement($sql);
                    if( $status !== true ) {
                        return $this->error('上传失败2');
                    }
                }
                $insert_data_array = [];
                $update_data_array = [];
                echo $count,":",time(),"<br>";
                if( $count % 20000 == 0 ) {
                    break;
                }
            }

        }
        //处理数据
        if($insert_data_array != null) {
            $insert_data = '';
            foreach ( $insert_data_array as $key => $value) {
                $insert_data .= "('{$value['business_identity_id']}','{$value['request_no']}','{$value['amount']}','{$value['status']}'),";
            }
            $insert_data = trim($insert_data,',');
            $sql = "insert into {$table_prefix}bill_detail_log (`business_identity_id`,`request_no`,`base_amount`,`status`) values {$insert_data}";
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

            $sql = "UPDATE {$table_prefix}bill_detail_log SET base_amount = CASE id {$when1} END , status = CASE id {$when2} END WHERE id IN ($ids)";
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
        $time_end = time();
        echo $count,":",$time_end - $time_start;
    }
}