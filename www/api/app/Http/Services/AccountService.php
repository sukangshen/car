<?php
/**
 * Created by PhpStorm.
 * User: tal
 * Date: 2022-04-01
 * Time: 23:03
 */

namespace App\Http\Services;


use App\Models\Admin\AccountRecharges;
use App\Models\Admin\Accounts;
use Illuminate\Support\Facades\DB;

class AccountService
{


    /**
     * 充值
     */
    public function recharge($params)
    {

        $userId = array_get($params, 'user_id', 0);
        $rawAmount = array_get($params, 'raw_amount', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空');
        }

        if ($rawAmount <= 0) {
            throw new \Exception('金额必须大于0');

        }

        try {
            //创建recharge一条记录
            DB::beginTransaction();
            $accountRechargesParams = [
                'user_id' => $userId,
                'raw_amount' => $rawAmount,
                'amount' => $rawAmount,
            ];
            $accountRechargesRes = AccountRecharges::query()->create($accountRechargesParams);

            //更新总金额
            $userAccount = Accounts::getAccountInfoByUserId($userId);
            if ($userAccount) {
                $userAccount->amount = $userAccount->amount + $rawAmount;
                $userAccount->cumulative_amount = $userAccount->cumulative_amount + $rawAmount;
                $userAccount->save();

            } else {
                Accounts::query()->create([
                    'user_id' => $userId,
                    'amount' => $rawAmount,
                    'cumulative_amount' => $rawAmount,
                ]);
            }
            DB::commit();

            return $accountRechargesRes->insertGetId($accountRechargesParams);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getFile() . '-' . $exception->getLine() . '-' . $exception->getMessage());
        }
    }


    /**
     * 消费
     */
    public function consume($params)
    {
        $userId = array_get($params, 'user_id', 0);
        $consumeAmount = array_get($params, 'consume_amount', 0);
        if (!$userId) {
            throw new \Exception('用户ID不能为空');
        }

        if ($consumeAmount <= 0) {
            throw new \Exception('金额必须大于0');
        }

        $uuId = md5(uniqid() . mt_rand(1, 1000000));

        //查询金额是否充足
        $userAccount = Accounts::getAccountInfoByUserId($userId);

        if (!$userAccount || $consumeAmount > $userAccount->amount) {
            throw new \Exception('金额不足，请充值', 400);
        }

        $ignoreIds = [];  //需要忽略的请求消耗的充值ID
        $moveAmount = $consumeAmount;
        //获取最先充值的
        $recharge = AccountRecharges::getOldestRechargeByUserId($userId);
        $errorStatus = 0;
        try {
            DB::beginTransaction();
            //充足开始扣费
            while (true) {
                list($positionRechargeId) = $recharge->moveWhenConsume($moveAmount, $uuId);
                if ($moveAmount == 0) {
                    break;
                }
                $ignoreIds[] = $positionRechargeId;

                $recharge = AccountRecharges::nextZBYRecharge($userId, $ignoreIds);
                if (!$recharge) {
                    $errorStatus = 1;
                    break;
                }
            }

            //更新账户总金额表
            $userAccount->updateByConsumeAmount($consumeAmount);


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

}
