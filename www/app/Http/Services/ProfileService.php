<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Services;

use App\Models\Resources;

class ProfileService
{
    /**
     * Desc:处理列表数据
     * User: kangshensu@gmail.com
     * Date: 2019-09-14
     * @param $profiles
     * @return array
     */
    public static function profileSearch($profiles)
    {
        if (empty($profiles)) {
            return [];
        }

        foreach ($profiles['data'] as $index => $item) {
            //调整图片
            $images = json_decode($item['resource'], true);
            $profiles['data'][$index]['wechat_img'] = QiniuService::getFilepathByArray($images['wechat_img']);
            $profiles['data'][$index]['self_img'] = QiniuService::getFilepathByArray($images['self_img']);
            unset($profiles['data'][$index]['resource']);
        }

        return $profiles ?: [];
    }


    /**
     * Desc:获取个人详情
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $data
     * @return array|mixed
     */
    public static function profileDetail($data)
    {
        if (empty($data)) {
            return [];
        }
        //获取个人信息资源
        $resources = Resources::query()->where('id', $data['resource_id'])->first()->toArray();

        if (empty($resources)) {
            return [];
        }

        $images = json_decode($resources['resource'], true);
        $data['wechat_img'] = QiniuService::getFilepathByArray($images['wechat_img']);
        $data['self_img'] = QiniuService::getFilepathByArray($images['self_img']);

        $data = UtilService::opz($data, ['created_at', 'updated_at', 'deleted_at', 'resource_id', 'end_time']);
        return $data;
    }


    /**
     * Desc:处理我的列表数据
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $profiles
     * @return array
     */
    public static function myProfileList($profiles)
    {
        if (empty($profiles)) {
            return [];
        }
        foreach ($profiles as $index => $item) {
            //调整图片
            $images = json_decode($item['resource'], true);
            $profiles[$index]['wechat_img'] = QiniuService::getFilepathByArray($images['wechat_img']);
            $profiles[$index]['self_img'] = QiniuService::getFilepathByArray($images['self_img']);
            unset($profiles[$index]['resource']);

            //判断是否过期
            if ($item['end_time'] > time()) {
                $profiles[$index]['profile_status_name'] = '进行中';
            } else {
                $profiles[$index]['profile_status_name'] = '已失效';
            }
        }

        return $profiles ?: [];
    }


}