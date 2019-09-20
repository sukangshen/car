<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller as Controller;

class MeController extends Controller
{

    /**
     * Desc:获取正在进行中的帖子
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myProfile(Request $request)
    {
        $user = auth('api')->user();
        //获取个人正在进行中的帖子
        $profile = Profile::query()->where('end_time', '>', time())->where('user_id', $user['id'])->get();
        return $this->success($profile);
    }

    public function myOrder(Request $request)
    {

    }

    /**
     * Desc:获取个人的信息
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request)
    {
        $user = auth('api')->user();
        return $this->success($user);
    }

}