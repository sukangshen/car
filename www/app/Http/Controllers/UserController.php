<?php

namespace App\Http\Controllers;

use App\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     * 要求附带email和password（数据来源users表）
     *
     * @return void
     */
    public function __construct()
    {
        // 这里额外注意了：官方文档样例中只除外了『login』
        // 这样的结果是，token 只能在有效期以内进行刷新，过期无法刷新
        // 如果把 refresh 也放进去，token 即使过期但仍在刷新期以内也可刷新
        // 不过刷新一次作废
        $this->middleware('auth:api', ['except' => ['login', 'register', 'auth', 'callback']]);
        // 另外关于上面的中间件，官方文档写的是『auth:api』
        // 但是我推荐用 『jwt.auth』，效果是一样的，但是有更加丰富的报错信息返回
    }


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
            'password' => 'required|min:6|max:255'
        ]);

        $user = User::add([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $token = auth('api')->login($user);
        return response()->json([
            'code' => 0,
            'message' => '注册成功',
            'token' => $token
        ]);
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
            'name' => 'required|min:1|max:255',
            'password' => 'required|min:6|max:255'
        ]);

        if (!$token = JWTAuth::attempt($request->all())) {
            return response(['error' => 'Account or password error.'], 400);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Desc:获取用户信息
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**退出
     * Desc:
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'code' => 0,
            'message' => '登出成功'
        ]);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
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

    /**
     * Desc:微信授权
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @param Request $request
     * @return mixed
     */
    public function auth(Request $request)
    {

        $targetUrl = $request->input('target_url');
        $config = config("wechat.official_account.default");
        $config['oauth']['scopes'] = ['snsapi_userinfo'];
        $config['oauth']['callback'] = 'http://love.anheqiaobei.com/api/callback' . '?target_url=http://love.anheqiaobei.com';

        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;

        // 未登录
        if (empty($_SESSION['wechat_user'])) {

            //$_SESSION['target_url'] = 'http://love.anheqiaobei.com';
            return $oauth->redirect();
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
            // $oauth->redirect()->send();
        }

    }


    /**
     * Desc:微信授权回调
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @param Request $request
     */
    public function callback(Request $request)
    {

        $targetUrl = $request->input('target_url');
        $config = config("wechat.official_account.default");
        $config['oauth']['scopes'] = ['snsapi_userinfo'];
        $config['oauth']['callback'] = 'http://love.anheqiaobei.com/api/callback';

        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;

        $userParams = [];
        $user = $oauth->user();

        $userInfo = $user->toArray();
        $userInfo = $userInfo['original'];

        $link = '?';
        if (strpos($targetUrl, '?') !== false) {
            $link = '&';
        }

        $userParams['openid'] = $userInfo['openid'];
        $userParams['nickname'] = trim($userInfo['nickname']);
        $userParams['gender'] = $userInfo['sex'] ?: 0;
        $userParams['headimgurl'] = $userInfo['headimgurl'];
        $userParams['city'] = $userInfo['city'];
        $userParams['province'] = $userInfo['province'];
        $userParams['country'] = $userInfo['country'];

        $userInfo = User::loginByOpenid($userParams['openid']);
        if (empty($userInfo)) {
            $userInfo = $createUser = User::add($userParams);
        }
        $token = JWTAuth::fromUser($userInfo);


        $url = sprintf('%s%stoken=%s&open_id=%s', $targetUrl, $link, ($token ?? ''), ($userParams['openid'] ?? ''));

        header('location:' . $url); // 跳转到 user/profile
    }


}