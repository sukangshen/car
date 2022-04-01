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

        //创建recharge一条记录
        DB::beginTransaction();
        try {

            AccountRecharges::query()->create([
                'user_id' => $userId,
                'raw_amount' => $rawAmount
            ]);

            //更新总金额
            $userAccount = Accounts::getAccountInfoByUserId($userId);
            if ($userAccount) {
                $userAccount->amount = $userAccount->amount + $rawAmount;
                $userAccount->cumulative_amount = $userAccount->cumulative_amount + $rawAmount;

            } else {
                Accounts::query()->create([
                    'user_id' => $userId,
                    'raw_amount' => $rawAmount,
                    'cumulative_amount' => $rawAmount,
                ]);

            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();

        }

    }


    /**
     * 消费
     */
    public function consume($params)
    {

    }

}