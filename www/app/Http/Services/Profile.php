<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-13
 */

namespace App\Http\Services;

use App\Models\Address;

class ProfileService
{
    public static function profileSearch($profiles)
    {
        if (empty($profiles)) {
            return [];
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