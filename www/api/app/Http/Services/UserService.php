<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-07
 */

namespace App\Http\Services;

use App\User;

class UserService
{
    /**
     * Desc:根据openId获取用户信息
     * User: kangshensu@gmail.com
     * Date: 2019-11-07
     * @param $openid
     * @return array
     */
    public function getUserInfoByOpenId($openid)
    {
        if (empty($openid)) {
            return [];
        }

        $userInfo = User::query()->where('openid', $openid)->first();
        if ($userInfo) {
            return $userInfo->toArray();
        }
        return [];
    }
}