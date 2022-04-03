<?php
/**
 * Created by PhpStorm.
 * User: tal
 * Date: 2022-04-01
 * Time: 22:25
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller as Controller;
use App\Http\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    protected $accountService;

    public function __construct(AccountService $accountService)
    {

        $this->accountService = $accountService;
    }


    public function recharge(Request $request)
    {
        try {
            return $this->success($this->accountService->recharge($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }

    }


    public function consume(Request $request)
    {
        try {
            return $this->success($this->accountService->consume($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }
    }

}
