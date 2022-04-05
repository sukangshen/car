<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/4/4
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller as Controller;
use App\Http\Services\AccountService;
use App\Http\Services\UserCardService;
use Illuminate\Http\Request;

class UserCardController extends Controller
{
    protected $userCardService;

    public function __construct(UserCardService $userCardService)
    {
        $this->userCardService = $userCardService;
    }


    public function recharge(Request $request)
    {
        try {
            return $this->success($this->userCardService->recharge($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }

    }


    public function consume(Request $request)
    {
        try {
            return $this->success($this->userCardService->consume($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }
    }

    public function rechargeQuery(Request $request)
    {
        try {
            return $this->success($this->userCardService->rechargeQuery($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }

    }


    public function consumeQuery(Request $request)
    {
        try {
            return $this->success($this->userCardService->consumeQuery($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }
    }

    public function cardSubjectQuery(Request $request)
    {
        try {
            return $this->success($this->userCardService->cardSubjectQuery($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }
    }

}
