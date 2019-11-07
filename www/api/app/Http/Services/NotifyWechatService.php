<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-07
 */

namespace App\Http\Services;

use App\Models\PaymentNotifyWechat;
use App\Models\PaymentOrder;

class NotifyWechatService
{
    /**
     * Desc:回复成功通知
     * User: kangshensu@gmail.com
     * Date: 2019-11-07
     */
    public static function replySuccess()
    {
        echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
        exit;
    }

    /**
     * Desc:回复失败通知
     * User: kangshensu@gmail.com
     * Date: 2019-11-07
     * @param $message
     */
    public static function replyFail($message)
    {
        echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[" . $message . "]]></return_msg></xml>";
        exit;
    }


    public function payNotify($data)
    {
        //更新订单状态
        $payOrder = PaymentOrder::query()->where('payment_sn',$data['out_trade_no'])->first();
        if(!$payOrder) {
            throw new \Exception('订单不存在');
        }
        $payOrder->trade_type = $data['trade_type'];
        $payOrder->pay_time = strtotime($data['time_end']);
        $payOrder->transaction_sn = $data['transaction_id'];
        $payOrder->status = WechatPaymentService::STATUS_PAY;
        $payOrder->bank_type = $data['bank_type'];
        $saveRes = $payOrder->save();
        if(!$saveRes) {
            throw new \Exception('订单不存在');
        }

        $payResult = new PaymentNotifyWechat();
        $payResult->appid = $data['appid'];
        $payResult->mch_id = $data['mch_id'];
        $payResult->device_info = isset($data['device_info'])?$data['device_info']:'';
        $payResult->nonce_str = $data['nonce_str'];
        $payResult->sign = $data['sign'];
        $payResult->sign_type = isset($data['sign_type'])?$data['sign_type']:'';
        $payResult->result_code = $data['result_code'];
        $payResult->err_code = isset($data['err_code'])?$data['err_code']:'';
        $payResult->err_code_des = isset($data['err_code_des'])?$data['err_code_des']:'';
        $payResult->openid = $openid = $data['openid'];
        $payResult->is_subscribe = isset($data['is_subscribe'])?$data['is_subscribe']:'';
        $payResult->trade_type = $data['trade_type'];
        $payResult->bank_type = $data['bank_type'];
        $payResult->total_fee = $total_fee = $data['total_fee'];
        $payResult->settlement_total_fee = $settlement_total_fee = isset($data['settlement_total_fee'])?$data['settlement_total_fee']:'';
        $payResult->fee_type = isset($data['fee_type'])?$data['fee_type']:'';
        $payResult->cash_fee = $cash_fee = $data['cash_fee'];
        $payResult->cash_fee_type = isset($data['cash_fee_type'])?$data['cash_fee_type']:'';
        $payResult->transaction_id = $data['transaction_id'];
        $payResult->out_trade_no = $out_trade_no = $data['out_trade_no'];
        $payResult->attach = $attach = isset($data['attach'])?$data['attach']:'';
        $payResult->time_end = $create_time = $data['time_end'];

        $res = $payResult->save();
        if (!$res) {
            throw new \Exception('记录日志失败');
        }
    }




}