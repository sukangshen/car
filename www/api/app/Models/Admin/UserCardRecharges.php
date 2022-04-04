<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/4
 */

namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCardRecharges extends Model
{
    use SoftDeletes;

    protected $table = 'user_card_recharges';

    protected $fillable = ['user_id', 'card_subject_id', 'times', 'remark', 'raw_times'];//开启白名单字段

    public static function saveData($params)
    {
        return self::query()->create($params);
    }

    public static function getOldestRechargeByUserIdAndSubjectId($userId, $cardSubjectId)
    {
        return self::query()
            ->where('times', '>', 0)
            ->where('user_id', $userId)
            ->where('card_subject_id', $cardSubjectId)
            ->orderBy('id', 'asc')
            ->first();
    }

    public function moveWhenConsume(&$moveTimes, $uuId)
    {
        //查询本次可用金额
        $availableTimes = $this->times;

        // 本次实际使用的值
        $step = ($availableTimes > $moveTimes ? $moveTimes : $availableTimes);
        $this->times -= $step;

        $moveTimes -= $step;

        //剩余次数
        $remainRechargeTimes = $availableTimes - $step;

        //增加 消耗的生成记录
        $consumeRechargeData = [
            'user_id' => $this->user_id,
            'uuid' => $uuId,
            'consume_times' => $step,
            'consume_recharge_id' => $this->id,
            'remain_recharge_amount' => $remainRechargeTimes,
            'card_subject_id' => $this->card_subject_id
        ];

        //消耗充值记录
        UserCardConsumeRecharge::saveData($consumeRechargeData);

        $this->save();

        return [$this->id];
    }

    public static function nextZBYRecharge($userId, $cardSubjectId, $ignoreIds = [])
    {
        return self::query()
            ->where('times', '>', 0)
            ->where('user_id', $userId)
            ->where('card_subject_id', $cardSubjectId)
            ->orderBy('id', 'asc')
            ->whereNotIn('id', $ignoreIds)
            ->first();
    }
}
