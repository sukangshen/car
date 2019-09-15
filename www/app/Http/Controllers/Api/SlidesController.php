<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-15
 */

namespace App\Http\Controllers\Api;

use App\Http\Services\UtilService;
use App\Models\Slides;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller as Controller;

class SlidesController extends Controller
{
    /**
     * Desc:获取首页轮播图
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlides(Request $request)
    {
        $slides = Slides::query()->orderBy('sort', 'desc')->get();
        return $this->success($slides);
    }
}