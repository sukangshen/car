<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-07
 */

namespace App\Http\Services;

use App\Models\Admin\Accounts;
use App\Models\Admin\Users;

class UserService
{


    public function query($params)
    {

        $columnName = array_get($params, 'column_name', '');
        $columnValue = array_get($params, 'column_value', '');
        $perPage = array_get($params, 'per_page', 10);


        $query = Users::query();
        if ($columnName && $columnValue) {
            $query->where($columnName, 'like', "%{$columnValue}%");

        }
        $query->orderBy('id', 'desc');
        $res = $query->paginate($perPage);

        return $res;
    }

    public function create($params)
    {

        $userMobile = array_get($params, 'user_mobile', '');
        if (!$userMobile) {
            throw new \Exception('手机号不能为空');
        }

        $userInfo = Users::query()->where('user_mobile', $userMobile)->first();
        if ($userInfo) {
            throw new \Exception('手机号已经存在');
        }

        return Users::query()->create($params);

    }

    public function detail($params)
    {
        $id = array_get($params, 'id', 0);
        if (!$id) {
            throw new \Exception('客户ID不能为空');
        }
        //用户信息
        $userInfo = Users::query()->where('id', $id)->first();

        //卡信息
        $userInfo['user_card_list'] = UserCardService::getCardListByUserId($id);

        //充值信息
        $accountInfo = Accounts::getAccountInfoByUserId($id);
        $userInfo['account_amount'] = $accountInfo ? $accountInfo->amount : 0;
        $userInfo['cumulative_amount'] = $accountInfo ? $accountInfo->cumulative_amount : 0;


        return $userInfo;
    }

    public function modify($params)
    {
        $id = array_get($params, 'id', 0);
        $userMobile = array_get($params, 'user_mobile', '');
        $userName = array_get($params, 'user_name', '');
        $plateNumber = array_get($params, 'plate_number', '');
        $remark = array_get($params, 'remark', '');

        if (!$id) {
            throw new \Exception('客户ID不能为空');
        }

        //用户信息
        $userInfo = Users::query()->where('id', $id)->first();
        if (!$userInfo) {
            throw new \Exception('客户不存在', 400);
        }

        //判断手机号是否重复
        if ($userMobile && $userInfo->user_mobile != $userMobile) {
            $userMobileExist = Users::query()
                ->where('id', '!=', $id)
                ->where('user_mobile', $userMobile)
                ->first();
            if ($userMobileExist) {
                throw new \Exception('新手机号已经在系统存在', 400);
            }
        }

        $modifyParams = [];

        if ($userMobile) {
            $modifyParams['user_mobile'] = $userMobile;
        }

        if ($userName) {
            $modifyParams['user_name'] = $userName;
        }

        if ($plateNumber) {
            $modifyParams['plate_number'] = $plateNumber;
        }
        if ($remark) {
            $modifyParams['remark'] = $remark;

        }

        return Users::query()->where('id', $id)->update($modifyParams);
    }

}
