<?php
/**
 * Created by PhpStorm.
 * User: tal
 * Date: 2022-04-01
 * Time: 23:08
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AccountConsumeRecharge extends Model
{

    protected $fillable = [
        'user_id',
        'consume_amount',
        'consume_recharge_id',
        'remain_recharge_amount',
        'uuid',
        'remark'
    ];//开启白名单字段

    protected $table = 'account_consume_recharge';


    public static function saveData($params)
    {
        return self::query()->create($params);
    }

}
