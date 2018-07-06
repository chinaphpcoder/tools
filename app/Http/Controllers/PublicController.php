<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

/**
 * 公共控制器
 */
class PublicController extends Controller{

    /**
     * CKEditor编辑器上传图片
     */
    public function uploadImage(Request $request){
        $image = $request->file('upload');

        if (empty($image)) {
            return [
                'uploaded' => 0,
                'error' => [
                    'message' => '没有发现要上传的文件',
                ]
            ];
        }

        //检测文件扩展
        $extension = $image->extension();
        if (in_array($extension, ['jpg', 'png', 'gif', 'jpeg']) === false) {
            return [
                'uploaded' => 0,
                'error' => [
                    'message' => '只允许上传图片',
                ]
            ];
        }

        try {
            $image_path = $image->store('article', 'oss');
        } catch (\Exception $e) {
            try {
                $image_path = $image->store('article', 'oss');
            } catch (\Exception $e) {
                return [
                    'uploaded' => 0,
                    'error' => [
                        'message' => '上传失败，请重试',
                    ]
                ];
            }
        }
        $image_url = "//" . config('filesystems.disks.oss.bucket') . "." . config('filesystems.disks.oss.endpoint') . "/" . $image_path;

        return [
            'uploaded' => 1,
            'fileName' => substr($image_path, 8), //去掉返回的变量里面的 'article/'
            'url' => $image_url,
        ];
    }

    /**
     * ckeditor编辑器上传图片
     * User: zhouyao
     * Date: 2018/3/8 下午5:51
     */
    public function ckeditorImage(Request $request)
    {
        $ckeditor = $this->uploadImage($request);

        if ($ckeditor['uploaded'] < 1) {
            echo '<font color="red" size="2">*' . $ckeditor['error']['message'] . '</font>';
            exit;
        }

        $callback = $_REQUEST["CKEditorFuncNum"];
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'". $ckeditor['url'] ."','');</script>";
    }
}