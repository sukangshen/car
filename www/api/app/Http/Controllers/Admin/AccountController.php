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
        return $this->accountService->recharge($request->all());

    }


    public function consume(Request $request)
    {
        return $this->accountService->consume($request->all());

    }

}