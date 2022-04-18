<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/3/29
 */

namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageItems extends Model
{
    use SoftDeletes;

    protected $table = 'package_items';


}
