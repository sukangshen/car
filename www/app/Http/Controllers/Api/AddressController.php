<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-11
 */
namespace App\Http\Controllers\Api;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    /**
     * Desc:获取城市列表
     * User: kangshensu@gmail.com
     * Date: 2019-09-12
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAddressList(Request $request)
    {
        $parentKey = $request->input('parent_key',0);
        $addressList = Address::query()->where('parent_key',$parentKey)->get();

        return $this->success($addressList);
    }
}