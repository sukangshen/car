<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/3/29
 */

namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardSubject extends Model
{
    use SoftDeletes;

    protected $table = 'card_subject';

    public static function findById($id)
    {
        return self::query()->where('id',$id)->first();
    }
}
