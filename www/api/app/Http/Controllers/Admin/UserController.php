<?php
/**
 * Desc:用户列表
 * User: kangshensu@gmail.com
 * Date: 2020-01-10
 */

namespace App\Http\Controllers\Admin;

use App\Http\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller as Controller;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {

        $this->userService = $userService;
    }

    public function query(Request $request)
    {
        return $this->success($this->userService->query($request->all()));

    }

    public function create(Request $request)
    {
        try {
            return $this->success($this->userService->create($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }

    }

    public function detail(Request $request)
    {
        return $this->success($this->userService->detail($request->all()));
    }

    public function modify(Request $request)
    {
        try {
            return $this->success($this->userService->modify($request->all()));
        } catch (\Exception $exception) {
            return $this->fail($exception->getCode(), $exception->getMessage());
        }

    }
}
