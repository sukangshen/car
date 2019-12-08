<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */

/**
 * 引入Auth中间件 验证是否授权
 */
Route::group(['middleware' => 'api.jwt.auth'], function () {
    //Token
    Route::post('logout', 'UserController@logout');
    Route::post('me', 'UserController@me');
    Route::post('refresh', 'UserController@refresh');

//    //发表帖子
//    Route::post('profileCreate', 'ProfileController@profileCreate');

    //校验发布权限
    Route::get('profileCreateCheck', 'ProfileController@profileCreateCheck');

    //获取我的信息
    Route::get('userInfo', 'MeController@userInfo');

    //获取我发布的帖子
    Route::get('myProfile', 'MeController@myProfile');

//    //获取我发布的帖子列表
//    Route::get('myProfileList', 'ProfileController@myProfileList');

});


/**
 * 帖子
 */
//发表帖子
Route::post('profileCreate', 'ProfileController@profileCreate');

//获取我发布的帖子列表
Route::get('myProfileList', 'ProfileController@myProfileList');

//获取帖子列表
Route::get('profileSearch', 'ProfileController@profileSearch');

//获取帖子详情
Route::get('profileDetail', 'ProfileController@profileDetail');

/**
 * 上传
 */
//七牛云图片上传
Route::any('img', 'UploadController@img');

//获取七牛云Token
Route::get('getQiniuToken', 'QiniuController@getQiniuToken');


/**
 * 我的
 */
//实名认证
Route::post('identityAuth', 'MeController@identityAuth');

//工作认证
Route::post('workAuth', 'MeController@workAuth');

//我的认证
Route::get('myAuthInfo', 'MeController@myAuthInfo');


/**
 * 微信
 */
//微信授权入口
Route::any('auth', 'UserController@auth');

//微信授权回调
Route::any('callback', 'UserController@callback');

//创建预支付订单'
Route::post('createOrder', 'WechatPaymentController@createOrder');

//支付回调
Route::any('payNotify', 'NotifyWechatController@payNotify');

//微信分享
Route::get('getTicket', 'WechatController@getTicket');

//微信推送
Route::any('serve', 'WechatController@serve');


/**
 * 公共
 */
//获取城市列表
Route::get('getAddressList', 'AddressController@getAddressList');

//获取轮播图推荐
Route::get('getSlides', 'SlidesController@getSlides');

//获取首页标签
Route::get('getHomePageLabel', 'HomePageLabelController@getHomePageLabel');

//获取标签分类列表
Route::get('getTagList', 'TagsController@getTagList');




















