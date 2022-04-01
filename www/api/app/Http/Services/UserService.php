<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-07
 */

namespace App\Http\Services;

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

        return Users::query()->insert($params);

    }

    public function detail($params)
    {
        $id = array_get($params, 'id', 0);
        if (!$id) {
            throw new \Exception('客户ID不能为空');
        }
        return Users::query()->where('id', $id)->first();
    }

}
