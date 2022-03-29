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


    public function userList(Request $request)
    {
        $query = User::query();
        $query->select();
        if(request('nickname')) {
            $query->where('nickname','like','%' . trim(request('nickname')) . '%');
        }

        if(request('user_name')) {
            $query->where('user_name','like','%' . trim(request('user_name')) . '%');
        }
        $query->orderBy('created_at', 'desc');
        $userList = $query->paginate($request->input('limit'))->toarray();
        return $this->success($userList);
    }
}
