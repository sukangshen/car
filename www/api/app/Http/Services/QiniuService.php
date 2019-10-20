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

class QiniuService
{

    /**
     * Desc:七牛云图片上传
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $filePath
     * @param $filename
     * @return string
     */
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
        try {
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


    /**
     * Desc:返回七牛云文件路径
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $fileName
     * @return string
     */
    public static function getFilepath($fileName)
    {
        return 'http://' . env('QINIU_URL') . '/' . $fileName;
    }

    /**
     * Desc:根据资源数组拼接资源完整路径
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $data
     * @return array
     */
    public static function getFilepathByArray($data)
    {
        if (empty($data)) {
            return [];
        }
        $responseArray = [];
        foreach ($data as $index => $item) {
            $responseArray[] = self::getFilepath($item);
        }

        return $responseArray;
    }

    public static function imgUpload($params)
    {
        if(empty($params)) {
            return [];
        }

        $response = [];
        foreach ($params  as $index =>$item) {
            $path = $item->store('public/images');

            $filePath = storage_path('app/' . $path);
            $fileName = md5(time() . 'ahqb') . '.' . self::getExtension($filePath);
            //上传到七牛
            $data['url'] = QiniuService::upload($filePath, $fileName);  //调用的全局函数
            $response[] = $fileName;
        }

        return $response;

    }

    /**
     * Desc:获取文件的后缀名
     * User: kangshensu@gmail.com
     * Date: 2019-09-14
     * @param $filename
     * @return mixed
     */
    public static function getExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }



}
