<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-07
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
//use Illuminate\Routing\Controller as Controller;
use  App\Http\Controllers\Api\Controller as Controller;

class WeChatController extends Controller
{

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
        $app->server->push(function ($message) {
            Log::info('微信推送数据' . $message);
            return "欢迎关注 overtrue！";
        });

        return $app->server->serve();
    }

    /**
     * 获取分享票据
     * @return mixed
     */
    public function getTicket(Request $request)
    {
        $jsApiList = [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'updateAppMessageShareData',
            'updateTimelineShareData'
        ];
        try {
            $app = app('wechat.official_account');
            $url = trim($request->input('url', $debug = false, $beta = false, $json = true));
            if (!empty($url)) {
                $url = urldecode($url);
            }
            $ticket = $app->jssdk->setUrl($url)->buildConfig($jsApiList, false);
            if (empty($ticket)) {
                throw new \Exception('ticket 获取失败');
            }
            $ticket = json_decode($ticket, true);
            $shareArr = [
                'appId' => $ticket['appId'] ?: config('wechat.payment.default.app_id'),
                'nonceStr' => $ticket['nonceStr'] ?: '',
                'timestamp' => $ticket['timestamp'] ?: time(),
                'signature' => $ticket['signature'] ?: '',
                'jsApiList' => $ticket['jsApiList'] ?: $jsApiList
            ];

            return $this->success($shareArr);
        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }

}
