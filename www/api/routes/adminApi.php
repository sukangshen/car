<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */
/**
 * 引入Auth中间件 验证是否授权'admin.jwt.auth'
 */
Route::group(['middleware' => ['assign.guard:admin','admin.jwt.auth']], function () {
    /**
     * 登录
     */
    //获取我的信息
    Route::get('userInfo', 'AdminUserController@userInfo');
    //退出
    Route::any('logout', 'AdminUserController@logout');

    /**
     * 用户相关
     */
    Route::get('userList', 'UserController@userList');


    Route::get('card_subject/query', 'CardSubjectController@query');


    Route::post('user/create', 'UserController@create');
    Route::post('user/modify', 'UserController@modify');
    Route::get('user/query', 'UserController@query');
    Route::get('user/detail', 'UserController@detail');

    //充值消费
    Route::post('account/recharge', 'AccountController@recharge');
    Route::post('account/consume', 'AccountController@consume');
    //列表
    Route::get('account/recharge/query', 'AccountController@rechargeQuery');
    Route::get('account/consume/query', 'AccountController@consumeQuery');

    //充值消费
    Route::post('user_card/recharge', 'UserCardController@recharge');
    Route::post('user_card/consume', 'UserCardController@consume');

    //列表
    Route::get('user_card/recharge/query', 'UserCardController@rechargeQuery');
    Route::get('user_card/consume/query', 'UserCardController@consumeQuery');
    Route::get('user_card/card_subject/query', 'UserCardController@cardSubjectQuery');


    Route::get('statistics/info', 'StatisticsController@info');


});



Route::any('register', 'AdminUserController@register');
Route::any('login', 'AdminUserController@login');

