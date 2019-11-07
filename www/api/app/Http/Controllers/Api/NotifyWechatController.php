<?php
/**
 * Desc:微信支付回调
 * User: kangshensu@gmail.com
 * Date: 2019-11-06
 */

namespace App\Http\Controllers\Api;

use App\Http\Services\NotifyWechatService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\Controller as Controller;
use EasyWeChat\Factory;

class NotifyWechatController extends Controller
{
    private $_notifyWechatService = null;


    public function getNotifyWechatService()
    {
        if (!$this->_notifyWechatService) {
            $this->_notifyWechatService = new NotifyWechatService();
        }
        return $this->_notifyWechatService;
    }


    public function payNotifyAction()
    {
        try {
            /* //$xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
             $xml = file_get_contents("php://input");
             if (empty($xml)) {
                 NotifyWechatService::replyFail('数据为空！');
             } else {
                 //将XML转为array 禁止引用外部xml实体
                 libxml_disable_entity_loader(true);
                 $params = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)),
                     true);
             }*/

            Log::info('--------微信支付回调--------');

            $config = config('wechat.payment.default');
            $response = Factory::payment($config)->handlePaidNotify(function ($message, $fail) use ($config) {
                $this->getNotifyWechatService()->payNotify($message);

            });
            return $response->send();
        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }


}