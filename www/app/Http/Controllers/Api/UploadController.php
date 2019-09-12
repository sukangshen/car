<?php
/**
 * Desc:七牛云对象存储
 * User: kangshensu@gmail.com
 * Date: 2019-09-07
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Services\Qiniu;
use App\Http\Controllers\Api\Controller as Controller;

class UploadController extends Controller
{

    public function img(Request $request)
    {

        if ($request->hasFile('image') && $request->image->isValid()) {
            $allow_types = ['image/png', 'image/jpeg', 'image/gif', 'image/jpg'];
            if (!in_array($request->image->getMiMeType(), $allow_types)) {
                return ['status' => 0, 'msg' => '图片类型不正确！'];
            }
            if ($request->image->getClientSize() > 1024 * 1024 * 3) {
                return ['status' => 0, 'msg' => '图片大小不能超过 3M'];
            }
            $path = $request->image->store('public/images');
//            //上传到本地
//            return ['status'=> 1, 'msg'=>'/storage'.str_replace('public', '', $path)];

            //storage_path返回根目录下的storage的绝对路径 里面放的直接丢在后面
            $filePath = storage_path('app/' . $path);
            $fileName = basename($filePath);

            //上传到七牛
            $data['url'] = Qiniu::upload($filePath, $fileName);  //调用的全局函数
            //返回
            return ['status' => 1, 'img_url' => env('QINIU_URL') . '/' . $fileName];

        }
    }


}
