<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */
/**
 * 引入Auth中间件 验证是否授权
 */
Route::group(['middleware' => 'admin.jwt.auth'], function () {
    //获取我的信息
    Route::get('userInfo', 'AdminUserController@userInfo');


});



//微信授权回调
Route::any('register', 'AdminUserController@register');
Route::any('login', 'AdminUserController@login');

