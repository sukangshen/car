<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/3/29
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Admin\Controller as Controller;
use App\Models\Admin\CardSubject;
use Illuminate\Http\Request;

class CardSubjectController extends Controller
{
    public function query(Request $request)
    {
        return $this->success(CardSubject::query()->get());
    }
}
