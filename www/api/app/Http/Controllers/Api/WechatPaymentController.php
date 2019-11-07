<?php
/**
 * Desc:微信下单中心
 * User: kangshensu@gmail.com
 * Date: 2019-10-27
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use  App\Http\Controllers\Api\Controller as Controller;
use Illuminate\Support\Facades\Validator;
use  App\Http\Services\WechatPaymentService;
use function EasyWeChat\Kernel\Support\generate_sign;

class WechatPaymentController extends Controller
{

    public function createOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'goods_name' => 'required',
                'openid' => 'required',
                'total_fee' => 'required',
            ]);
            if ($validator->fails()) {
                $this->fail(400);
            }
            $params = $request->all();

            $wechatPaymentService = new WechatPaymentService();
            $unify = $wechatPaymentService->createOrder($params);
            if ($unify['return_code'] === 'SUCCESS' && !isset($unify['err_code'])) {
                $pay = [
                    'appId' => config('wechat.payment.default.app_id'),
                    'timeStamp' => (string)time(),
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

        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }
}