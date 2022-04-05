<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/4
 */

namespace App\Http\Services;


use App\Models\Admin\CardSubject;
use App\Models\Admin\UserCard;
use App\Models\Admin\UserCardConsumeRecharge;
use App\Models\Admin\UserCardRecharges;
use Illuminate\Support\Facades\DB;

class UserCardService
{

    public function recharge($params)
    {
        $userId = array_get($params, 'user_id', 0);
        $cardSubjectId = array_get($params, 'card_subject_id', 0);
        $times = array_get($params, 'recharge_times', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空');
        }

        if (!$cardSubjectId) {
            throw new \Exception('消费项目ID不能为空');
        }

        if ($times <= 0) {
            throw new \Exception('充值次数必须大于0');
        }

        //查询项目是否存在
        $cardSubject = CardSubject::findById($cardSubjectId);
        if (!$cardSubject) {
            throw new \Exception('充值项目不存在');
        }

        try {
            //创建recharge一条记录
            DB::beginTransaction();
            $userCardRechargesParams = [
                'user_id' => $userId,
                'card_subject_id' => $cardSubjectId,
                'times' => $times,
                'raw_times' => $times
            ];
            UserCardRecharges::saveData($userCardRechargesParams);

            //更新总金额
            $userCard = UserCard::getUserCardInfoByUserId($userId, $cardSubjectId);
            if ($userCard) {
                $userCard->times = $userCard->times + $times;
                $userCard->cumulative_times = $userCard->cumulative_times + $times;
                $userCard->save();

            } else {
                UserCard::query()->create([
                    'user_id' => $userId,
                    'times' => $times,
                    'card_subject_id' => $cardSubjectId,
                    'cumulative_times' => $times,
                ]);
            }
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getFile() . '-' . $exception->getLine() . '-' . $exception->getMessage());
        }

    }


    public function consume($params)
    {

        $userId = array_get($params, 'user_id', 0);
        $cardSubjectId = array_get($params, 'card_subject_id', 0);
        $times = array_get($params, 'consume_times', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空');
        }

        if (!$cardSubjectId) {
            throw new \Exception('消费项目ID不能为空');
        }

        if ($times <= 0) {
            throw new \Exception('消费次数必须大于0');
        }

        //查询项目是否存在
        $cardSubject = CardSubject::findById($cardSubjectId);
        if (!$cardSubject) {
            throw new \Exception('充值项目不存在');
        }

        //查询次数是否充足
        $userCard = UserCard::getUserCardInfoByUserId($userId, $cardSubjectId);
        if (!$userCard || $userCard->times < $times) {
            throw new \Exception('卡次数不足，请充值', 400);
        }

        $uuId = md5(uniqid() . mt_rand(1, 1000000));

        $ignoreIds = [];  //需要忽略的请求消耗的充值ID
        $moveTimes = $times;
        //获取最先充值的
        $recharge = UserCardRecharges::getOldestRechargeByUserIdAndSubjectId($userId, $cardSubjectId);
        $errorStatus = 0;
        try {
            DB::beginTransaction();
            //充足开始扣费
            while (true) {
                list($positionRechargeId) = $recharge->moveWhenConsume($moveTimes, $uuId);
                if ($moveTimes == 0) {
                    break;
                }
                $ignoreIds[] = $positionRechargeId;

                $recharge = UserCardRecharges::nextZBYRecharge($userId, $cardSubjectId, $ignoreIds);
                if (!$recharge) {
                    $errorStatus = 1;
                    break;
                }
            }

            //更新账户总次数表
            $userCard->updateByConsumeTimes($times);

            if ($errorStatus) {
                DB::rollBack();
                return false;
            } else {
                DB::commit();
                return true;
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getFile() . '-' . $exception->getLine() . '-' . $exception->getMessage());
        }

    }


    public static function getCardListByUserId($userId)
    {
        $cardList = UserCard::query()
            ->select([
                'user_id',
                'card_subject_id',
                'card_subject.name as card_subject_name',
                'times',
                'cumulative_times'
            ])
            ->join('card_subject', 'card_subject.id', '=', 'user_card.card_subject_id')
            ->where('user_id', $userId)
            ->get()->toArray();
        return $cardList ?? [];
    }


    public function rechargeQuery($params)
    {
        $perPage = array_get($params, 'per_page', 10);
        $userId = array_get($params, 'user_id', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空', 400);
        }

        return UserCardRecharges::query()
            ->select(['user_card_recharges.*', 'card_subject.name as card_subject_name'])
            ->join('card_subject', 'card_subject.id', '=', 'user_card_recharges.card_subject_id')
            ->where('user_id', $userId)
            ->orderBy('user_card_recharges.id', 'desc')
            ->paginate($perPage);
    }

    public function consumeQuery($params)
    {
        $userId = array_get($params, 'user_id', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空', 400);
        }

        $perPage = array_get($params, 'per_page', 10);
        $userId = array_get($params, 'user_id', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空', 400);
        }

        return UserCardConsumeRecharge::query()
            ->select(['user_card_consume_recharge.*', 'card_subject.name as card_subject_name'])
            ->join('card_subject', 'card_subject.id', '=', 'user_card_consume_recharge.card_subject_id')
            ->where('user_id', $userId)
            ->orderBy('user_card_consume_recharge.id', 'desc')
            ->paginate($perPage);
    }


    public function cardSubjectQuery($params)
    {
        $userId = array_get($params, 'user_id', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空', 400);
        }

        $cardList = CardSubject::query()
            ->select([
                'user_id',
                'card_subject_id',
                'card_subject.name as card_subject_name',
                'times',
                'cumulative_times'
            ])
            ->leftJoin('user_card', 'card_subject.id', '=', 'user_card.card_subject_id')
            ->where('user_id', $userId)
            ->get()->toArray();
        return $cardList ?? [];
    }

}
