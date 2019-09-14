<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Services;

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
            $wechatImg = json_decode($images['wechat_img'], true);
            $selfImg = json_decode($images['self_img'], true);
            $profiles['data'][$index]['wechat_img'] = [];
            $profiles['data'][$index]['self_img'] = [];

            if (!empty($wechatImg)) {
                foreach ($wechatImg as $key => $value) {
                    $profiles['data'][$index]['wechat_img'][] = 'http://' . env('QINIU_URL') . '/'.$value;
                }
            }

            if (!empty($selfImg)) {
                foreach ($selfImg as $k => $val) {
                    $profiles['data'][$index]['self_img'][] = 'http://' . env('QINIU_URL') .'/'.$val;
                }
            }
            unset($profiles['data'][$index]['resource']);
        }
        return $profiles ?: [];
    }


}