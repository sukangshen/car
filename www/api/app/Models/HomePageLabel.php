<?php
/**
 * Desc:首页标签
 * User: kangshensu@gmail.com
 * Date: 2019-09-15
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SoftDeletesEx as SoftDeletes;

class HomePageLabel extends Model
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
    protected $table = 'home_page_label';

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



