<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'resources';

    protected $guarded = [];

    /**
     * @param \DateTime|int $value
     * @return false|int
     * @author dividez
     */
    public function fromDateTime($value){
        return strtotime(parent::fromDateTime($value));
    }
}
