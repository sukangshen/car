<?php
/**
 * Desc:七牛云对象存储
 * User: kangshensu@gmail.com
 * Date: 2019-09-07
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use zgldh\QiniuStorage\QiniuStorage;

class UploadController extends UserController
{
    public $disk = '';

    public function index(Request $request)
    {
        $user  = $request->user();
        $this->disk = QiniuStorage::disk('qiniu');
        $data = [];
        // 获取到文件信息
        $file = !empty($_FILES['fileUpload']) ? $_FILES['fileUpload'] : [];
        if (empty($file)) {
            return '';
        }
        // 获取文件名
        $fileName = $file['name'];
        $TmpName = $file['tmp_name'];
        // 获取文件内容
        $fp = fopen($TmpName, "r");
        $contents = fread($fp, filesize($TmpName));
        $this->disk->put($fileName, $contents);                        //上传文件
        $data['url'] = $this->disk->downloadUrl($fileName);      //获取下载地址
        $data['ur1'] = $this->disk->privateDownloadUrl($fileName);
        return $this->response('true', 1, $data);
    }
}
