<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/18
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller as Controller;
use App\Models\Admin\PackageItems;
use Illuminate\Http\Request;


class PackageItemsController extends Controller
{
    public function query(Request $request)
    {
        return $this->success(PackageItems::query()->select(['id','id as package_items_id','name','created_at','updated_at'])->get());
    }
}
