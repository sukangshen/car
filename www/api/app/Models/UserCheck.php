<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-17
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCheck extends Model
{
    const STATUS_TO_AUDIT       = 1;    //待审核
    const STATUS_AUDIT_PASS     = 2;    //审核通过
    const STATUS_AUDIT_UN_PASS  = 2;    //审核未通过

    const SOURCE_IDENTITY_IMG   = 1;    //  身份认证
    const SOURCE_WORK_IMG       = 2;    //  工作认证

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_check';

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
