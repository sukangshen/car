<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SoftDeletesEx as SoftDeletes;

class Tags extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'tags';

    protected $guarded = [];

    /**
     * @param \DateTime|int $value
     * @return false|int
     * @author dividez
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }
}