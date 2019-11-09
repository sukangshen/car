<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-09
 */

namespace App\Http\Controllers\Admin;

use App\Models\Admin\AdminUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller as Controller;

class AdminUserController extends Controller
{
    /**
     * Desc:注册
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:1|max:255',
            'email' => 'required|min:1|max:255',
            'password' => 'required|min:6|max:255',
            'phone' => 'required|min:6|max:255',
        ]);

        $user = AdminUser::add([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
        ]);
        $token = auth('admin')->login($user);
        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);

    }

    /**
     * Desc:获取用户信息
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo()
    {
        return $this->success(auth('admin')->user());
    }

    /**
     * Desc:登录
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {

        $request->validate([
            'phone' => 'required|min:1|max:255',
            'password' => 'required|min:6|max:255'
        ]);
        $token = auth('admin')->attempt($request->all());

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);

    }
}