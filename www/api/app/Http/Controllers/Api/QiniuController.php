<?php
/**
 * Desc:七牛云上传返回前端Token
 * User: kangshensu@gmail.com
 * Date: 2019-12-08
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as Controller;
use Illuminate\Http\Request;
// 引入鉴权类
use Qiniu\Auth;

class QiniuController extends Controller
{
    public function getQiniuToken(Request $request)
    {
        try {

            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = env('QINIU_ACCESS_KEY');
            $secretKey = env('QINIU_SECRET_KEY');
            $bucket = env('QINIU_BUCKET');

            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);

            // 生成上传 Token
            $token = $auth->uploadToken($bucket);

            return $this->success(['upload_token' => $token]);

        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }
}
