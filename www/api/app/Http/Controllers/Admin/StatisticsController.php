<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/6
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Admin\Controller as Controller;
use App\Http\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function info(Request $request)
    {
        try {
            return $this->success($this->statisticsService->info($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }
    }

}
