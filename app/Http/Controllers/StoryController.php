<?php
namespace App\Http\Controllers;

use App\Http\Models\Comments;
use App\Http\Models\Story;
use function array_merge;
use function dd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use function is_array;
use function json_decode;
use function json_encode;
use PhpParser\Comment;
use function route;
use function substr;
use function substr_replace;
use function var_dump;
use function view;

class StoryController extends Controller
{
    public $type_name = null;

    /**
     * 设置类型转换
     * StoryController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $type = $request->input('type', 1);

        switch ($type) {
            case 1:
                $this->type_name = '小沙投资';
                break;
            case 2:
                $this->type_name = '小沙风控';
                break;
            default:
                $this->type_name = '小沙投资';
                break;
        }
    }

    /**
     * 小沙故事列表显示
     * User: zhouyao
     * Date: 2018/3/8 下午3:45
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 1);

        $lists = Story::query()->where(['type' => $type])
            ->orderBy('attr', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $this->view_data['meta_title'] = $this->type_name;
        $this->view_data['type'] = $type;
        $this->view_data['lists'] = $lists;
        return view('story.index', $this->view_data);
    }

    /**
     * 添加小沙故事视图
     * User: zhouyao
     * Date: 2018/3/8 下午4:16
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request)
    {
        $type = $request->input('type', 1);

        $this->view_data['meta_title'] = $this->type_name;
        $this->view_data['type'] = $type;
        return view('story.add', $this->view_data);
    }

    /**
     * 编辑文章视图
     * User: zhouyao
     * Date: 2018/3/9 下午4:12
     * @param Request $request
     * @return mixed
     */
    public function edit(Request $request)
    {
        $id = $request->input('id', 0);
        $type = $request->input('type', 1);

        if ($id < 0 || !is_numeric($id)) {
            return $this->error('没有获取到有效ID');
        }

        $row = Story::query()->where('id', $id)->first();
        if (empty($row)) {
            return $this->error('没有获取到指定文章');
        }

        $this->view_data['row'] = $row;
        $this->view_data['meta_title'] = '编辑' . $this->type_name;
        $this->view_data['type'] = $type;
        return view('story.edit', $this->view_data);
    }

    /**
     * 处理添加与修改逻辑
     * User: zhouyao
     * Date: 2018/3/9 上午9:53
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $id = $request->input('id', 0);
        //验证输入的合法性
        $this->validate($request, [
            'title' => 'required|min:1|max:40',
            'content' => 'required|min:1'
        ]);



        if ($id < 1) {
            $story = new Story();
        } else {
            $story = Story::query()->find($id);
        }

        $status =  isset($_GET['status']) ? $_GET['status'] : intval($request->input('status', 1));
        $story->title = $request->input('title');
        $story->type = $request->input('type');
        $story->user_id = Auth::id();
        $story->published_at = date('Y-m-d H:i:s');
        $story->status = $status;
        $story->attr = $request->input('attr');
        $story->content = $request->input('content');

        //检测是否有上传图片
        $image_url = $request->file('pic');
        if (!empty($image_url)) {
            try {
                $image_path = $image_url->store('article', 'oss');
            } catch (\Exception $e) {
                $image_path = $image_url->store('article', 'oss');
            }
            $story->pic = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;
        }

        $result = $story->save();

        if ($result) {
            return $this->success(route('story_index', ['type' => $request->input('type')]));
        } else {
            return $this->error('操作失败');
        }
    }

    /**
     * 删除小沙故事
     * User: zhouyao
     * Date: 2018/3/9 下午5:20
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $ids = explode(',', $id);

        if (empty($ids)) {
            return $this->error('请选择要删除的记录');
        }
        $result = Story::destroy($ids);

        if ($result) {
            return $this->success(route('story_index', ['type' => $type]), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }


    /**
     * 编辑小沙故事文章状态
     * User: zhouyao
     * Date: 2018/3/12 上午11:51
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function status(Request $request)
    {
        $id = $request->input('id', 0);
        $type = $request->input('type', 1);
        $status = $request->input('status', 0);

        if ($id < 0 || $status < 0 ) {
            return $this->error('操作有误，请重试。。。');
        }

        switch ($status) {
            case 1: //取消隐藏
                $data = ['status' => 1];
                break;
            case 2: //隐藏
                $data = ['status' => 2];
                break;
            case 3: //取消置顶
                $data = ['attr' => 3];
                break;
            case 4: //取消精品
                $data = ['attr' => 3];
                break;
            case 5: //设为置顶
                $data = ['attr' => 1];
                break;
            case 6: //设为精品
                $data = ['attr' => 2];
                break;
            default:
                $data = [];
                break;
        }

        if (empty($data)) {
            return $this->error('操作有误，请重试。。。');
        }

        $result = Story::query()->where('id','=', $id)->update($data);

        if ($result) {
            return $this->success(route('story_index', ['type' => $type]), '操作成功');
        } else {
            return $this->error('操作失败');
        }
    }

    /**
     * 查看评论
     * User: zhouyao
     * Date: 2018/3/18 下午5:41
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function message(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id < 1) {
            return $this->error('非法操作');
        }
        $data = [];

        $message = DB::table('sxs_cms.comment')->where('article_story_id', $id)
            ->orderBy('log_time', 'desc')
            ->get();
        $message = json_decode(json_encode($message), true);
        if (!empty($message)) {
            foreach ($message as $key => $val) {
                if ($val['parent_id'] == 0) { //求出文章评论
                    $data[$val['id']] = $message[$key];
                }
                //求出用户id
                $ids[] = $val['user_id'];
            }

            foreach ($message as $key => $val) {
                if ($val['parent_id'] == 0) {
                    continue;
                }
                $data[$val['parent_id']]['child'][] = $val;
            }

            $user = DB::table('sxs_vault.vault_user')
                ->whereIn('id', $ids)
                ->select(['id', 'mobile', 'micon'])
                ->get();
            $user = json_decode(json_encode($user), true);
            foreach ($user as $key => $val) {
                $mobile[$val['id']] = $val['mobile'];
                $micon[$val['id']] = $val['micon'];
            }

            foreach ($data as $key => $val) {
                if (empty($micon[$val['user_id']])) {
                    $micon = 'https://sxstest.oss-cn-beijing.aliyuncs.com/usericon/head.png';
                } else {
                    $micon = $micon[$val['user_id']];
                }
                $data[$key]['mobile'] = substr_replace($mobile[$val['user_id']], '****', 3, 4);
                $data[$key]['micon'] = $micon;

                if (empty($val['child'])) {
                    continue;
                }

                foreach ($val['child'] as $k => $v) {
                    if (!is_array($v)) {
                        continue;
                    }
                    $data[$key]['child'][$k]['mobile'] = substr_replace($mobile[$v['user_id']], '****', 3, 4);
                    $data[$key]['child'][$k]['reply_mobile'] = substr_replace($mobile[$v['reply_user_id']], "****", 3, 4);
                }
            }
        }

        $this->view_data['data'] = $data;
        return view('story.message', $this->view_data);
    }

    /**
     * 删除评论
     * User: zhouyao
     * Date: 2018/3/18 下午5:41
     * @param Request $request
     * @return mixed
     */
    public function deleteMessage(Request $request)
    {
        $id = $request->input('id');
        $comment_id = $request->input('comment_id');

        $story = Comments::query()->where('article_story_id', $id)->get()->toArray();
        $data = self::getChildrenIds($story, $comment_id, true);

        if (empty($data)) {
            return $this->error('没有找到需要删除的评论');
        }

        $result = Comments::destroy($data);

        if ($result) {
            //统计最新的评论数量
            $comment_count = Comments::query()->where('article_story_id', $id)->where('comment_id', 0)->count();
            Story::query()->where('id','=', $id)->update(['comments' => $comment_count]);
            return $this->success(route('story_message', ['id' => $id]), '删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /**
     * 递归根据父级ID查找所有子级ID
     * User: zhouyao
     * Date: 2018/3/19 上午9:45
     * @param $data
     * @param $f_id
     * @param bool $flag    是否包括父级自己的ID 默认不包括
     * @return array
     */
    protected static function getChildrenIds($data, $f_id, $flag = false)
    {
        if (empty($data)) {
            return $data;
        }
        $arr = [];
        if ($flag) {
            $arr[] = $f_id;
        }

        foreach ($data as $val) {
            if ($val['comment_id'] == $f_id) {
                $arr[] = $val['id'];
                $arr = array_merge($arr, self::getChildrenIds($data, $val['id']));
            }
        }

        return $arr;
    }
}