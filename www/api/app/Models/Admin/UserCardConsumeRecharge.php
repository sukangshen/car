<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/4
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCardConsumeRecharge extends Model
{
    use SoftDeletes;


    protected $table = 'user_card_consume_recharge';

    protected $fillable = [
        'user_id',
        'card_subject_id',
        'consume_recharge_id',
        'remain_recharge_times',
        'consume_times',
        'uuid',
        'remark'
    ];//开启白名单字段


    public static function saveData($params)
    {
        return self::query()->create($params);
    }
}
