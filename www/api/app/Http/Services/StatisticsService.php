<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/6
 */

namespace App\Http\Services;


use App\Models\Admin\AccountConsumeRecharge;
use App\Models\Admin\AccountRecharges;
use App\Models\Admin\UserCardConsumeRecharge;
use App\Models\Admin\UserCardRecharges;
use App\Models\Admin\Users;

class StatisticsService
{

    const  DATE_TYPE_DAY = 1;
    const  DATE_TYPE_WEEK = 2;
    const  DATE_TYPE_MONTH = 3;
    const  DATE_TYPE_YEAR = 4;
    const  DATE_TYPE_TAL = 5;

    public function info($params)
    {

        $dateType = array_get($params, 'date_type', self::DATE_TYPE_DAY);

        switch ($dateType) {
            case self::DATE_TYPE_DAY:
                $startTime = date('Y-m-d');
                $endTime = date("Y-m-d 23:59:59");
                break;
            case self::DATE_TYPE_WEEK:
                $startTime = date('Y-m-d', strtotime('-' . date('w') . ' days'));
                $endTime = date('Y-m-d 23:59:59', strtotime('+' . (6 - date('w')) . ' days'));
                break;
            case self::DATE_TYPE_MONTH:
                $startTime = date('Y-m-01');
                $endTime = date('Y-m-t 23:59:59');
                break;
            case self::DATE_TYPE_YEAR:
                $startTime = date('Y') . '-01-01';
                $endTime = date('Y') . '-12-31 23:59:59';
                break;
            case self::DATE_TYPE_TAL:
                $startTime = '2022-01-01';
                $endTime = date("Y-m-d 23:59:59");;
                break;
            default :
                $startTime = date('Y-m-d');
                $endTime = date("Y-m-d", strtotime('tomorrow'));
        }
        //本日 本周 本月 本年 累计
        //充卡次数 消卡次数 充值金额 消费金额

        $data = [];

        //充卡次数
        $data['recharge_times'] = UserCardRecharges::query()
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->sum('raw_times') ?? 0;

        //扣卡次数
        $data['consume_times'] = UserCardConsumeRecharge::query()
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->sum('consume_times') ?? 0;


        //充值金额 = 储值—+卡
        $rechargeAmount = AccountRecharges::query()
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->sum('raw_amount') ?? 0;

        $cardRechargeAmount = UserCardRecharges::query()
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->sum('amount') ?? 0;
        $data['recharge_times'] = $rechargeAmount + $cardRechargeAmount;


        //消费金额
        $data['consume_amount'] = AccountConsumeRecharge::query()
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->sum('consume_amount') ?? 0;

        //新会员
        $data['member_cnt'] = Users::query()
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->count('id') ?? 0;

        return $data;
    }


}
