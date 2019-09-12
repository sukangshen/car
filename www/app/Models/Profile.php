<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SoftDeletesEx as SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    const END_TIME = 7;//定义展示时间
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
    protected $table = 'profile';

    protected $guarded = [];

    /**
     * @param \DateTime|int $value
     * @return false|int
     * @author dividez
     */
    public function fromDateTime($value){
        return strtotime(parent::fromDateTime($value));
    }

    public function resource()
    {
        return $this->hasOne('App\Models\Resources');
    }


}
