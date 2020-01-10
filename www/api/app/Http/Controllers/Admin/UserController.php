<?php
/**
 * Desc:用户列表
 * User: kangshensu@gmail.com
 * Date: 2020-01-10
 */

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller as Controller;

class UserController extends Controller
{

    /**
     * Desc:获取用户列表
     * User: kangshensu@gmail.com
     * Date: 2020-01-10
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userList(Request $request)
    {
        $query = User::query();
        $query->select();
        $query->orderBy('created_at', 'desc');
        $userList = $query->paginate($request->input('limit'))->toarray();
        return $this->success($userList);
    }
}
