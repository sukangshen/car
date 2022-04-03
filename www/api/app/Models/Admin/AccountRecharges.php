<?php
/**
 * Created by PhpStorm.
 * User: tal
 * Date: 2022-04-01
 * Time: 23:08
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AccountRecharges extends Model
{

    protected $table = 'account_recharges';

    protected $fillable = ['user_id', 'raw_amount', 'amount', 'remark'];//开启白名单字段


    public static function getOldestRechargeByUserId($userId)
    {
        return self::query()
            ->where('amount', '>', 0)
            ->where('user_id', $userId)
            ->orderBy('id', 'asc')
            ->first();
    }

    public function moveWhenConsume(&$moveAmount, $uuId)
    {
        //查询本次可用金额
        $availableAmount = $this->amount;

        // 本次实际使用的值
        $step = ($availableAmount > $moveAmount ? $moveAmount : $availableAmount);
        $this->amount -= $step;

        $moveAmount -= $step;

        //剩余金额
        $remainRechargeAmount = $availableAmount - $step;

        //增加 消耗的生成记录
        $consumeRechargeData = [
            'user_id' => $this->user_id,
            'uuid' => $uuId,
            'consume_amount' => $step,
            'consume_recharge_id' => $this->id,
            'remain_recharge_amount' => $remainRechargeAmount,
        ];

        //消耗充值记录
        AccountConsumeRecharge::saveData($consumeRechargeData);

        $this->save();

        return [$this->id];
    }

    public static function nextZBYRecharge($userId, $ignoreIds = [])
    {
        return self::query()
            ->where('amount', '>', 0)
            ->where('user_id', $userId)
            ->orderBy('id', 'asc')
            ->whereNotIn('id', $ignoreIds)
            ->first();
    }
}
