<?php
/**
 * Created by PhpStorm.
 * User: tal
 * Date: 2022-04-01
 * Time: 23:08
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accounts extends Model
{

    use SoftDeletes;

    protected $fillable = ['user_id', 'amount', 'cumulative_amount', 'remark'];//开启白名单字段

    protected $table = 'accounts';

    public static function getAccountInfoByUserId($userId)
    {
        return self::query()->where('user_id', $userId)->first();

    }

    public function updateByConsumeAmount($consumeAmount)
    {
        $this->amount -= $consumeAmount;
        $this->save();
    }
}
