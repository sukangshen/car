<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/4
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCard extends Model
{
    use SoftDeletes;

    protected $table = 'user_card';

    protected $fillable = ['user_id', 'card_subject_id', 'times', 'remark','cumulative_times'];//开启白名单字段


    public static function getUserCardInfoByUserId($userId, $cardSubjectId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('card_subject_id', $cardSubjectId)
            ->first();

    }

    public function updateByConsumeTimes($times)
    {
        $this->times -= $times;
        $this->save();
    }


}
