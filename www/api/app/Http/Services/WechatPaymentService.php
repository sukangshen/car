<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-06
 */

namespace App\Http\Services;

use App\Models\PaymentOrder;

class WechatPaymentService
{

    //支付订单状态
    const STATUS_INIT = 0;
    const STATUS_PAY = 1;
    const STATUS_CANCEL = 2;
    const STATUS_REVERSE = 3;


    const TRADE_TYPE_JSAPI = 'JSAPI';
    const TRADE_TYPE_APP = 'APP';

    const PAY_TYPE_WECHAT = 1;  //微信支付

    public function createOrder($params)
    {
        $userService = new UserService();
        $userInfo = $userService->getUserInfoByOpenId($params['openid']);
        if (empty($userInfo)) {
            throw new \Exception('获取用户信息失败');
        }
        $paymentSn = UtilService::createSerialNo();
        $app = app('wechat.payment');
        $unify = $app->order->unify([
            'body' => $params['goods_name'],
            'out_trade_no' => $paymentSn,
            'total_fee' => $params['total_fee'],
            'notify_url' => config('wechat.payment.default.notify_url'),
            'trade_type' => self::TRADE_TYPE_JSAPI,
            'openid' => $params['openid'],
        ]);

        $paymentOrderParams = [];
        $paymentOrderParams['payment_sn'] = $paymentSn;
        $paymentOrderParams['user_id'] = $userInfo['id'];
        $paymentOrderParams['goods_id'] = 0;
        $paymentOrderParams['money'] = $params['total_fee'];
        $paymentOrderParams['pay_type'] = self::PAY_TYPE_WECHAT;
        $paymentOrderParams['trade_type'] = self::TRADE_TYPE_JSAPI;
        $paymentOrderParams['status'] = self::STATUS_INIT;
        //创建订单
        $createResult = PaymentOrder::query()->create($paymentOrderParams);
        if (!$createResult) {
            throw new \Exception('创建订单失败');
        }

        return $unify;
    }


}