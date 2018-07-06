<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use DB;

class WorldcupController extends Controller
{
    protected $DB;
    protected $sign;
    public function __construct()
    {
        parent::__construct();
        $this->DB = DB::connection('vault');
        $this->sign = 'WC20180614';
    }

    /*
     * 球队管理
     * User: yangming
     * Date: 2018/6/03
     */
    public function footballTeam(Request $request) {
        $result = $this->DB->table('vault_football_team')->orderBy('id','desc')->get();
        $result = json_decode(json_encode($result), true);
        $this->view_data['data'] = $result;
        $this->view_data['meta_title'] = '球队管理';
        return view('worldcup.footballTeam', $this->view_data);
    }

    /*
     * 新增球队页面
     */
    public function add() {

        $this->view_data['meta_title'] = '新增球队';
        $this->view_data['data'] = [
            'id' => -1,
            'pic' => '',
            'team_name' => ''
        ];
        return view('worldcup.add', $this->view_data);
    }

    /*
     * 球队编辑页面
     */
    public function edit(Request $request) {
        $id = $request->input('id', 0);
        if(empty($id)) {
            $this->error('参数错误，请返回刷新重试');
        }
        $team = $this->DB->table('vault_football_team')->where('id', '=', $id)->first();
        $team = json_decode(json_encode($team), true);

        $this->view_data['data'] = $team;
        $this->view_data['meta_title'] = '球队管理';
        return view('worldcup.add', $this->view_data);
    }

    /*
     * 删除球队
     */
    public function delteam(Request $request) {
        $id = $request->input('id', null);
        if(empty($id)) {
            $this->error('参数错误，请返回刷新重试');
        }
        $this->DB->table('vault_football_team')->where('id', '=', $id)->delete();

        return $this->success(route('team_list'), '删除成功');
    }

    /**
     * @Synopsis createTeam 编辑球队
     *
     * @Param $request
     *
     * @Return
     */
    public function createTeam(Request $request)
    {
        // 球队名称
        $team_name = $request->input('team_name', '');
        if (empty($team_name)) {
            return $this->error('请填写球队名称');
        }
        // 球队id 有则修改，没有则添加
        $id = intval($request->input('id', 0));
        // 如果是添加，则必上传图标
        $pic = $request->file('file');
        if (1 > $id && !$pic) {
            return $this->error('请上传队标');
        }

        // 要修改的数据
        $data = array();
        // 如果有值，则上传
        if ($pic) {
            $image_path = $pic->store('worldcup', 'oss');
            $pic_path = "//" . config('filesystems.disks.oss.bucket') .
                "." . config('filesystems.disks.oss.endpoint') .
                "/" . $image_path;
            $data['pic'] = $pic_path;
        }

        // 判断是否修改，默认添加
        $is_update = false;

        if (0 < $id) {
            // 查询数据
            $football_team = DB::connection("vault")
                ->table('vault_football_team')
                ->where('id', '=', $id)
                ->get();

            $football_team = json_decode(json_encode($football_team),true);
            // 判断如果没有新的上传，和队名未修改，则返回
            if (!$pic && isset($football_team[0]['team_name']) && $football_team[0]['team_name'] == $team_name) {
                return $this->error('未做任何更改');
            }
            // 如果队名不一致，则修改队名
            if (isset($football_team[0]['team_name']) && $team_name != $football_team[0]['team_name']) {
                $data['team_name'] = $team_name;
            }
            // 查询有数据，则修改，无数据则插入
            if (count($football_team) > 0) {
                $is_update = true;
            } else {
                return $this->error('未查到该记录');
            }
        }
        if ($is_update) {
            $result = DB::connection('vault')->table('vault_football_team')->where('id','=',$id)->update($data);
        } else {
            $data['team_name'] = $team_name;
            $result = DB::connection('vault')->table('vault_football_team')->insertGetId($data);
        }
        if (!$result) {
            return $this->error('处理失败,请重试');
        }

        return $this->success(route('team_list'),'处理成功');

    }

    /*
     * 赛制管理
     */
    public function match() {
        $result = $this->DB->table('vault_match_log as match')
            //->field('team.team_name as team1 ,team2.team_name as team2')
            ->leftJoin('vault_football_team as team','match.team_id1','=','team.id')
            ->leftJoin('vault_football_team as team2','match.team_id2','=','team2.id')
            ->select(['match.id', 'match.type', 'match.match_time','team.team_name as team1', 'team2.team_name as team2','match.result'])
            ->orderBy('match.id','desc')
            ->get();
        $result = json_decode(json_encode($result), true);
        foreach($result as $key => $value) {
            if($value['result'] == 'team_id1') {
                $result[$key]['result'] = $value['team1'];
            }elseif ($value['result'] == 'team_id2') {
                $result[$key]['result'] = $value['team2'];
            }elseif ($value['result'] == 'dogfall') {
                $result[$key]['result'] = "平局";
            }else{
                $result[$key]['result'] = "未知";
            }
            $result[$key]['type'] = $this->switch_type($value['type']);
        }
        $this->view_data['data'] = $result;
        $this->view_data['meta_title'] = '赛制管理';
        return view('worldcup.match', $this->view_data);
    }
    private function switch_type($type) {
        $typeName = '';
        switch ($type) {
            case 1 : $typeName = '小组赛';break;
            case 2 : $typeName = '1/8决赛';break;
            case 3 : $typeName = '1/4决赛';break;
            case 4 : $typeName = '半决赛';break;
            case 5 : $typeName = '3、4名决赛';break;
            case 6 : $typeName = '总决赛';break;
            default : $typeName = '未知';break;
        }
        return $typeName;
    }

    /*
     * 增加赛制页面
     */
    public function add_match() {
        $team = $this->DB->table("vault_football_team")->get();
        $team = json_decode(json_encode($team), true);
        $this->view_data['list'] = $team;
        $this->view_data['data']['type'] = 1;
        $this->view_data['meta_title'] = "增加赛制";
        return view('worldcup.add_match',$this->view_data);
    }
    /*
     * 增加赛制
     */
    public function doadd_match(Request $request) {
        $data['type']       = $request->input('type');
        $data['match_time'] = $request->input('match_time');
        $data['team_id1']   = $request->input('team_id1');
        $data['team_id2']   = $request->input('team_id2');
        $data['result']     = $request->input('result');
        $data['first']      = $request->input('first');
        $params = '';
        foreach($data as $key => $value) {
            if(empty($value)) {
                $params = $key;
                break;
            }
        }
        if(!empty($params)) {
            return $this->error('表单不能有空项');
        }

        $id = $this->DB->table('vault_match_log')->insertGetId($data);
        if($id <= 0) {
            return $this->error('处理失败，请稍后再试');
        }

        return $this->success(route('world_team_match'), "处理成功");
    }

    /*
     * 编辑赛制页
     */
    public function worldMatchEdit(Request $request) {
        $id = $request->input('id', 0);

        if($id <= 0) {
            return $this->error("参数错误");
        }

        $match = $this->DB->table('vault_match_log')->where('id','=',$id)->first();
        $match = json_decode(json_encode($match), true);

        $team = $this->DB->table("vault_football_team")->get();
        $team = json_decode(json_encode($team), true);

        $this->view_data['list'] = $team;
        $this->view_data['data'] = $match;
        $this->view_data['meta_title'] = "编辑赛制";

        return view("worldcup.edit_match", $this->view_data);
    }

    /*
     * 确认编辑
     */
    public function worldDoeditMatch (Request $request) {
        $data['id'] = $id = $request->input('id');
        $data['type'] = $request->input('type');
        $data['match_time'] = $request->input('match_time');
        $data['team_id1'] = $request->input('team_id1');
        $data['team_id2'] = $request->input('team_id2');
        $data['result'] = $request->input('result');
        $data['first'] = $request->input('first');


        $param = '';
        foreach ($data as $key => $val) {
            if(empty($val)) {
                $param = $key;
                break;
            }
        }

        if(!empty($param)) {
            return $this->error('表单不能有空项');
        }

        $match = $this->DB->table('vault_match_log')->where('id','=',$id)->first();
        $match = json_decode(json_encode($match), true);

        $param = false;
        foreach ($data as $key => $value) {
            if($match[$key] != $value) {
                $param = true;
                break;
            }
        }
        if($param === false) {
            return $this->error("表单无任何修改");
        }

        unset($data['id']);
        $update = $this->DB->table('vault_match_log')
            ->where('id','=', $id)
            ->update($data);
        if(!$update) {
            return $this->error("处理失败，请稍后再试");
        }

        // 为修改比赛结果
        if($data['result'] == 'no' && $data['first'] == 'no') {
            return $this->success(route('world_team_match'),"处理成功");
        }

        //用户竞猜状态
        $guessing = $this->DB->table('vault_guessing')->where('match_id','=',$id)->get();
        $guessing = json_decode(json_encode($guessing), true);
        // 没有用户竞猜， 只需修改比赛状态over
        if(count($guessing) <= 0 ) {
            return $this->success(route('world_team_match'),"处理成功");
        }
        else {
            if($data['result'] != 'no') {
                $update = $this->DB->table('vault_guessing')->where('match_id','=',$id)
                    ->where('result','=',$data['result'])
                    ->update(['guessing' => 1]);
                if(!$update) {
                    Log::error('sql执行失败 或 不存在where条件的数据');
                }
            }
            if($data['first'] != 'no') {
                $update = $this->DB->table('vault_guessing')->where('match_id','=',$id)
                    ->where('first','=',$data['first'])
                    ->update(['first_guessing' => 1]);
                if(!$update) {
                    Log::error('sql执行失败 或 不存在where条件的数据');
                }
            }

            Log::error('已修改竞猜结果');
            return $this->success(route('world_team_match'),"处理成功");
        }
    }

    //删除赛事
    public function worldMatchDelete(Request $request) {
        $id = $request->input('id', 0);
        if($id <= 0) {
            return $this->error("删除失败，请刷新重试");
        }
        $result = $this->DB->table('vault_match_log')->where('id', '=', $id)->delete();

        if(!$result) {
            return $this->error("删除失败，请刷新重试");
        }
        return $this->success(route("world_team_match"), '删除成功');
    }

    //中奖用户管理--小组赛
    public function worldGroup(Request $request) {
        $user_id = $request->input('user_id');
        $mobile = $request->input('mobile');
        //搜索条件

        $db_user = $this->DB->table('vault_user_prize_log as p');
        //查询用户名对应的记录
        if (!empty($user_id)) {
            $search_user = $this->DB->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
            if( !isset($search_user[0]) ) {
                $tmp_user_id = 0;
            } else {
                $tmp_user_id = $search_user[0];
            }
            $db_user->where('user_id', '=', $tmp_user_id);
        }

        //查询手机号对应的记录
        if (!empty($mobile)) {
            $search_mobile = $this->DB->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
            if( !isset($search_mobile[0]) ) {
                $tmp_user_id = 0;
            } else {
                $tmp_user_id = $search_mobile[0];
            }
            $db_user->where('user_id', '=', $tmp_user_id);
        }
        //获取活动ID
        $activity_id = $this->getActivityId();

        // 获奖记录
        $logs = $db_user->leftJoin('vault_user as u','p.user_id','=','u.id')
            ->where('p.prize_ext5','=','1')
            ->where('p.activity_id','=',$activity_id)
            ->select('p.id','p.user_id','p.prize_ext4','p.prize_ext5','u.real_name','u.mobile')
            ->paginate(20);
        $list = $logs;
        $logs = json_decode(json_encode($logs), true);

        $this->view_data['list'] = $logs;
        $this->view_data['page'] = $list;
        $this->view_data['meta_title'] = "中奖用户管理";
        // 没有获奖记录直接返回
        if(count($logs['data']) <= 0) {
            return view('worldcup.group',$this->view_data);
        }

        $user_ids = [];
        foreach($logs['data'] as $key => $val) {
            $user_ids[] = $val['user_id'];
        }

        // 累计猜对场数
        $guess = $this->DB->table("vault_guessing")
            ->whereIn('user_id',$user_ids)
            ->where('guessing','=','1')
            ->groupBy('user_id')
            ->select(DB::raw('count(*) as count'),'user_id')
            ->get();
        $guess = json_decode(json_encode($guess), true);

        $arr = [];
        foreach ($guess as $key => $val) {
            $arr[$val['user_id']] = $val['count'];
        }

        foreach ($logs['data'] as $key => $val) {
            $logs['data'][$key]['prize_ext5'] = $this->switch_type($val['prize_ext5']);
            $logs['data'][$key]['count'] = isset($arr[$val['user_id']]) ? $arr[$val['user_id']] : '';
        }

        //收货地址
        $address = $this->DB->table('vault_address')
            ->whereIn('user_id', $user_ids)
            ->where('activity_id','=',$activity_id)
            ->select('name','phone','address','user_id')
            ->get();

        $address = json_decode(json_encode($address), true);

        $add = [];
        foreach ($address as $key => $value) {
            $add[$value['user_id']] = $value;
        }

        foreach ($logs['data'] as $key => $val) {
            $logs['data'][$key]['sh_name'] = isset($add[$val['user_id']]['name']) ? $add[$val['user_id']]['name'] : '';
            $logs['data'][$key]['address'] = isset($add[$val['user_id']]['address']) ? $add[$val['user_id']]['address'] : '';
            $logs['data'][$key]['sh_phone'] = isset($add[$val['user_id']]['phone']) ? $add[$val['user_id']]['phone'] : '';
        }

        $this->view_data['list'] = $logs;
        $this->view_data['page'] = $list;
        $this->view_data['meta_title'] = "中奖用户管理";
        return view('worldcup.group',$this->view_data);
    }
    private function getActivityId(){
        $activity_id = $this->DB->table("vault_activity")
            ->where('activity_identification','=', $this->sign)
            ->select('id')
            ->first();
        if(empty($activity_id->id)) {
            return 0;
        }
        return $activity_id->id;
    }

    //数据导出
    public function export() {
        //获取活动ID
        $activity_id = $this->getActivityId();

        $db_user = $this->DB->table('vault_user_prize_log as p');
        // 获奖记录
        $logs = $db_user->leftJoin('vault_user as u','p.user_id','=','u.id')
            ->where('p.prize_ext5','=','1')
            ->where('p.activity_id','=',$activity_id)
            ->select('p.id','p.user_id','p.prize_ext4','p.prize_ext5','u.real_name','u.mobile')
            ->get();
        $logs = json_decode(json_encode($logs), true);

        $user_ids = [];
        foreach($logs as $key => $val) {
            $user_ids[] = $val['user_id'];
        }

        // 累计猜对场数
        $guess = $this->DB->table("vault_guessing")
            ->whereIn('user_id',$user_ids)
            ->where('guessing','=','1')
            ->groupBy('user_id')
            ->select(DB::raw('count(*) as count'),'user_id')
            ->get();
        $guess = json_decode(json_encode($guess), true);

        $arr = [];
        foreach ($guess as $key => $val) {
            $arr[$val['user_id']] = $val['count'];
        }

        foreach ($logs as $key => $val) {
            $logs[$key]['count'] = $arr[$val['user_id']];
        }

        //收货地址
        $address = $this->DB->table('vault_address')
            ->whereIn('user_id', $user_ids)
            ->where('activity_id','=',$activity_id)
            ->select('name','phone','address','user_id')
            ->get();

        $address = json_decode(json_encode($address), true);

        $add = [];
        foreach ($address as $key => $value) {
            $add[$value['user_id']] = $value;
        }

        foreach ($logs as $key => $val) {
            $logs[$key]['sh_name'] = isset($add[$val['user_id']]['name']) ? $add[$val['user_id']]['name'] : '';
            $logs[$key]['address'] = isset($add[$val['user_id']]['address']) ? $add[$val['user_id']]['address'] : '';
            $logs[$key]['sh_phone'] = isset($add[$val['user_id']]['phone']) ? $add[$val['user_id']]['phone'] : '';
        }

        //根据业务，自己进行模板赋值。
        $file_name   = "中奖用户名单-".date("Y-m-d H:i:s",time());
        $file_suffix = "xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file_name.$file_suffix");

        return view("worldcup.export", ['list' => $logs]);
    }

    /*
     * 中奖用户管理-- 决赛
     */
    public function finals(Request $request) {
        $user_id = $request->input('user_id');
        $mobile = $request->input('mobile');
        //搜索条件

        $db_user = $this->DB->table('vault_user_prize_log as p');
        //查询用户名对应的记录
        if (!empty($user_id)) {
            $search_user = $this->DB->table('vault_user')->where('id', '=', $user_id)->pluck('id')->toArray();
            if( !isset($search_user[0]) ) {
                $tmp_user_id = 0;
            } else {
                $tmp_user_id = $search_user[0];
            }
            $db_user->where('p.user_id', '=', $tmp_user_id);
        }

        //查询手机号对应的记录
        if (!empty($mobile)) {
            $search_mobile = $this->DB->table('vault_user')->where('mobile', '=', $mobile)->pluck('id')->toArray();
            if( !isset($search_mobile[0]) ) {
                $tmp_user_id = 0;
            } else {
                $tmp_user_id = $search_mobile[0];
            }
            $db_user->where('p.user_id', '=', $tmp_user_id);
        }
        //获取活动ID
        $activity_id = $this->getActivityId();

        // 获奖记录
        $logs = $db_user->leftJoin('vault_user as u','p.user_id','=','u.id')
            ->where('p.prize_ext5','>','1')
            ->where('p.activity_id','=',$activity_id)
            ->select('p.id','p.user_id','p.prize_ext4','p.prize_ext5','u.real_name','u.mobile')
            ->groupBy('p.user_id')
            ->groupBy('p.prize_ext5')
            ->paginate(20);
        $list = $logs;
        $logs = json_decode(json_encode($logs), true);
        //获奖用户ID
        $user_ids = [];
        foreach ($logs['data'] as $key => $val) {
            $user_ids[] = $val['user_id'];
        }
        //只猜对球队场数
        $guessing = $this->DB->table('vault_guessing')
            ->whereIn('user_id', $user_ids)
            ->where('guessing','=','1')
            ->where('first_guessing','=','0')
            ->where('type','>','1')
            ->select(DB::raw('count(*) as count'),'user_id','type')
            ->groupBy('user_id')
            ->groupBy('type')
            ->get();
        $guessing = json_decode(json_encode($guessing), true);
        foreach ($guessing as $key => $value) {
            $guessing[$value['user_id'].':'.$value['type']] = $value;
        }
        //只猜对进第一球球队场数
        $first_guessing = $this->DB->table('vault_guessing')
            ->whereIn('user_id', $user_ids)
            ->where('first_guessing','=','1')
            ->where('guessing','=','0')
            ->where('type','>','1')
            ->select(DB::raw('count(*) as count'),'user_id','type')
            ->groupBy('user_id')
            ->groupBy('type')
            ->get();
        $first_guessing = json_decode(json_encode($first_guessing), true);

        foreach ($first_guessing as $key => $value) {
            $first_guessing[$value['user_id'].':'.$value['type']] = $value;
        }
        //全部猜对
        $all = $this->DB->table('vault_guessing')
            ->whereIn('user_id', $user_ids)
            ->where('first_guessing','=','1')
            ->where('guessing','=','1')
            ->where('type','>','1')
            ->select(DB::raw('count(*) as count'),'user_id','type')
            ->groupBy('user_id')
            ->groupBy('type')
            ->get();
        $all = json_decode(json_encode($all), true);
        foreach ($all as $key => $value) {
            $all[$value['user_id'].':'.$value['type']] = $value;
        }


        foreach ($logs['data'] as $key=>$value) {
            $logs['data'][$key]['count'] = isset($guessing[$value['user_id'].':'.$value['prize_ext5']]['count']) ? $guessing[$value['user_id'].':'.$value['prize_ext5']]['count'] : 0;
            $logs['data'][$key]['first_count'] = isset($first_guessing[$value['user_id'].':'.$value['prize_ext5']]['count']) ? $first_guessing[$value['user_id'].':'.$value['prize_ext5']]['count']:0;
            $logs['data'][$key]['sum'] = isset($all[$value['user_id'].':'.$value['prize_ext5']]['count']) ? $all[$value['user_id'].':'.$value['prize_ext5']]['count']:0;
        }

        foreach ($logs['data'] as $key=>$val) {
            $logs['data'][$key]['prize_ext5'] = $this->switch_type($val['prize_ext5']);
        }
        $this->view_data['list'] = $logs;
        $this->view_data['page'] = $list;
        $this->view_data['meta_title'] = "中奖用户管理";
        return view('worldcup.finals',$this->view_data);
    }

    /*
     * 决赛导出
     */
    public function finals_export() {
        $db_user = $this->DB->table('vault_user_prize_log as p');
        //获取活动ID
        $activity_id = $this->getActivityId();

        // 获奖记录
        $logs = $db_user->leftJoin('vault_user as u','p.user_id','=','u.id')
            ->where('p.prize_ext5','>','1')
            ->where('p.activity_id','=',$activity_id)
            ->select('p.id','p.user_id','p.prize_ext4','p.prize_ext5','u.real_name','u.mobile')
            ->get();

        $logs = json_decode(json_encode($logs), true);
        //获奖用户ID
        $user_ids = [];
        foreach ($logs as $key => $val) {
            $user_ids[] = $val['user_id'];
        }
        //只猜对球队场数
        $guessing = $this->DB->table('vault_guessing')
            ->whereIn('user_id', $user_ids)
            ->where('guessing','=','1')
            ->select(DB::raw('count(*) as count'),'user_id')
            ->groupBy('user_id')
            ->get();
        $guessing = json_decode(json_encode($guessing), true);
        foreach ($guessing as $key => $value) {
            $guessing[$value['user_id']] = $value;
        }
        //只猜对进第一球球队场数
        $first_guessing = $this->DB->table('vault_guessing')
            ->whereIn('user_id', $user_ids)
            ->where('first_guessing','=','1')
            ->select(DB::raw('count(*) as count'),'user_id')
            ->groupBy('user_id')
            ->get();
        $first_guessing = json_decode(json_encode($first_guessing), true);

        foreach ($first_guessing as $key => $value) {
            $first_guessing[$value['user_id']] = $value;
        }

        foreach ($logs as $key=>$val) {
            $logs[$key]['count'] = $guessing[$val['user_id']]['count'];
            $logs[$key]['first_count'] = isset($first_guessing[$val['user_id']]['count']) ? $first_guessing[$val['user_id']]['count']:'';
        }

        foreach ($logs as $key=>$val) {
            $logs[$key]['sum'] = $val['count'] + $val['first_count'];
            $logs[$key]['prize_ext5'] = $this->switch_type($val['prize_ext5']);
        }
        $file_name   = "中奖用户名单-".date("Y-m-d H:i:s",time());
        $file_suffix = "xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file_name.$file_suffix");

        return view("worldcup.finals_export", ['list' => $logs]);
    }
}
