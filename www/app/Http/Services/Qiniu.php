<?php
/**
 * Desc:七牛云文件上传
 * User: kangshensu@gmail.com
 * Date: 2019-09-08
 */

namespace App\Http\Services;

// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class Qiniu
{
    public static function upload($filePath, $filename)
    {

        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = env('QINIU_ACCESS_KEY');
        $secretKey = env('QINIU_SECRET_KEY');
        $bucket = env('QINIU_BUCKET');

        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        //    $filePath = './php-logo.png';

        // 上传到七牛后保存的文件名
//        $key = basename($filePath);

        try{
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            $result = $uploadMgr->putFile($token, $filename, $filePath);
            //删除本地图片
            unlink($filePath);
            if (isset($result[0]) && isset($result[0]['key'])) {
                return $result[0]['key'];
            }
            return '';
        } catch (\Exception $e) {
            return '';
        }




    }
}
