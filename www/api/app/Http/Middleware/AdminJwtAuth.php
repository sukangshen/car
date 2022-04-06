<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;

class AdminJwtAuth
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;
    /**
     * Create a new BaseMiddleware instance.
     *
     * @param \Tymon\JWTAuth\JWTAuth  $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            return $this->fail(401, 'token_not_provided'); //缺少令牌
        }
        try {
            if (!$user = $this->auth->parseToken()->authenticate()) {
                return $this->fail(404, 'user_not_found');
            }
        } catch (TokenExpiredException $e) {
            return $this->fail(401, 'token_expired'); //令牌过期
        } catch (JWTException $e) {
            return $this->fail(401, 'token_invalid'); //令牌无效
        }

        return $next($request);
    }
    /**
     * Fire event and return the response.
     *
     * @param  string   $error
     * @param  int      $status
     * @return mixed
     */
    protected function respond($error, $status)
    {
        return response()->json(['error' => $error], $status);
    }

    public function success($data = [])
    {
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => config('errorcode.code')[200],
            'data' => $data,
        ]);
    }

    public function fail($code, $msg = '', $data = [])
    {
        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => !empty($msg) ? $msg : config('errorcode.code')[(int)$code],
            'data' => $data,
        ]);
    }



}
