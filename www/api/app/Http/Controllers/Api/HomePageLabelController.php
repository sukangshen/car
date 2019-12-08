<?php
/**
 * Desc:首页标签相关
 * User: kangshensu@gmail.com
 * Date: 2019-12-08
 */

namespace App\Http\Controllers\Api;

use App\Models\HomePageLabel;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller as Controller;

class HomePageLabelController extends Controller
{

    public function getHomePageLabel(Request $request)
    {
        try {
            $ret = HomePageLabel::query()->select(['id', 'title', 'target_url', 'sort'])->orderBy('sort',
                'asc')->get();

            return $this->success($ret);
        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }
}
