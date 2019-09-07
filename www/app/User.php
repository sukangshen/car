<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'openid',
        'gender',
        'nickname',
        'headimgurl',
        'city',
        'province',
        'country'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 获取将存储在JWT主题声明中的标识符。
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 返回一个键值数组，其中包含要添加到JWT的任何自定义声明。
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function add($data) {
        $user = self::create($data);
        return $user;
    }


    /**
     * Desc:直接校验OpenId不验证密码
     * User: kangshensu@gmail.com
     * Date: 2019-09-07
     * @param $openId
     * @return mixed
     */
    public function loginByOpenid($openId){
       return  self::where('openid','=',$openId)->first();
    }
}
