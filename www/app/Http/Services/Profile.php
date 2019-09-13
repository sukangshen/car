<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-13
 */

namespace app\Http\Services;

use App\Models\Address;

class ProfileService
{
    public static function profileSearch($profiles)
    {
        if (empty($profiles)) {
            return [];
        }
        if (!empty($profiles)) {
            $addressKeys = array_column($profiles['data'],'address') ?:[];
            $address = self::getAddressByAddressKey($addressKeys);
            foreach ($profiles['data'] as $index => $item) {
                //调整图片
                $images = json_decode($item['resource'], true);
                $profiles['data'][$index]['wechat_img'] = $images['wechat_img'];
                $profiles['data'][$index]['self_img'] = $images['self_img'];
                unset($profiles['data'][$index]['resource']);

                //获取家庭地址
//                $addressList =
            }
        }
    }

    public static function getAddressByAddressKey($data)
    {
        if(empty($data)) {
            return [];
        }
        $addressKey = [];

        foreach ($data as $index =>$item) {
            $firstKey = explode('-',$item);
            if($firstKey) {
                $addressKey[] = $firstKey[0];
            }
        }

        //批量查询
        $address = Address::query()->where('address_key',$addressKey)->get();
        print_r($address);die;

    }

}