<?php
/**
 * Desc:微信下单中心
 * User: kangshensu@gmail.com
 * Date: 2019-10-27
 */

namespace App\Http\Controllers\Api;

use App\Http\Services\UtilService;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use  App\Http\Controllers\Api\Controller as Controller;
use Illuminate\Support\Facades\Validator;
use function EasyWeChat\Kernel\Support\generate_sign;

class WechatPaymentController extends Controller
{

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goods_name' => 'required',
            'openid' => 'required',
            'total_fee' => 'required',
        ]);
        if ($validator->fails()) {
            $this->fail(400);
        }

        $app = app('wechat.payment');
        $unify = $app->order->unify([
            'body' => $request->goods_name,
            'out_trade_no' => UtilService::createSerialNo(),
            'total_fee' => $request->total_fee,
            'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action',
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request->openid,
        ]);

        if ($unify['return_code'] === 'SUCCESS' && !isset($unify['err_code'])) {
            $pay = [
                'appId' => config('wechat.payment.default.app_id'),
                'timeStamp' => (string) time(),
                'nonceStr' => $unify['nonce_str'],
                'package' => 'prepay_id=' . $unify['prepay_id'],
                'signType' => 'MD5',
            ];

            $pay['sign'] = generate_sign($pay, config('wechat.payment.default.key'));
            return $this->success($pay);
        } else {
            $unify['return_code'] = 'FAIL';
            return $this->success($unify);
        }

    }
}