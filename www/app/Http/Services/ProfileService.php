<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Services;

use App\Models\Address;

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
            $profiles['data'][$index]['wechat_img'] = [];
            $profiles['data'][$index]['self_img'] = [];

            if (!empty($images['wechat_img'])) {
                foreach ($images['wechat_img'] as $key => $value) {
                    $profiles['data'][$index]['wechat_img'][] = 'http://' . env('QINIU_URL') . '/' . $value;
                }
            }

            if (!empty($images['self_img'])) {
                foreach ($images['self_img'] as $k => $val) {
                    $profiles['data'][$index]['self_img'][] = 'http://' . env('QINIU_URL') . '/' . $val;
                }
            }
            unset($profiles['data'][$index]['resource']);
        }
        return $profiles ?: [];
    }


    public static function profileDetail($data)
    {
        if (empty($data)) {
            return [];
        }

        //根据address_key获取地址名称
        $addressKeys = explode('-', $data['address']);
        $addressNames = Address::query()->whereIn('address_key', $addressKeys)->orderBy('address_key',
            'asc')->get(['address_name'])->toArray();

        $a = array_values($addressNames);
        print_r($a);die;
        $addressNameString = '';
        if(!empty($addressNames)) {
            foreach ($addressNames as $index =>$item) {
                $addressNameString = $addressNameString.$item['address_name'];
            }
        }

        echo $addressNameString;die;
    }


}