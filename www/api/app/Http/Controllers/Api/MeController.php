<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\Resources;
use App\Models\UserCheck;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller as Controller;
use Illuminate\Support\Facades\Log;

class MeController extends Controller
{

    /**
     * Desc:获取正在进行中的帖子
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myProfile(Request $request)
    {
        $user = auth('api')->user();
        //获取个人正在进行中的帖子
        $profile = Profile::query()->where('end_time', '>', time())->where('user_id', $user['id'])->get();
        return $this->success($profile);
    }

    public function myOrder(Request $request)
    {

    }

    /**
     * Desc:获取个人的信息
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request)
    {
        $user = auth('api')->user();
        return $this->success($user);
    }

    /**
     * Desc:身份认证
     * User: kangshensu@gmail.com
     * Date: 2019-11-17
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function identityAuth(Request $request)
    {
        try {
            $params = $request->all();
            $params = array_filter($params);
            Log::info('身份认证' . print_r($params, true));
            $user = auth('api')->user();
            $userId = $user['id'] ?: 1;
            if (empty($params['user_name'])) {
                throw new \Exception('姓名不能为空');
            }
            if (empty($params['id_number'])) {
                throw new \Exception('身份证号不能为空');
            }
            if (empty($params['image_front'])) {
                throw new \Exception('身份证正面不能为空');
            }
            if (empty($params['image_back'])) {
                throw new \Exception('身份证反面不能为空');
            }

            $resourceParams['user_id'] = $userId;
            $resourceParams['source'] = Resources::SOURCE_IDENTITY_IMG;
            $params['image'] = [];
            $params['image'][] = $params['image_front'];
            $params['image'][] = $params['image_back'];
            $resourceParams['resource'] = json_encode($params['image']);
            $resourceCreate = Resources::query()->create($resourceParams);
            if (!$resourceCreate) {
                throw new \Exception('上传失败');
            }

            //创建用户身份关联审核表
            $userCheckParams = [];
            $userCheckParams['ext_id'] = $resourceCreate->id;
            $userCheckParams['user_id'] = $userId;
            $userCheckParams['status'] = UserCheck::STATUS_TO_AUDIT; //待审核
            $userCheckParams['source'] = UserCheck::SOURCE_IDENTITY_IMG; //身份认证
            $userCheckCreate = UserCheck::query()->create($userCheckParams);
            if (!$userCheckCreate) {
                throw new \Exception('关联审核失败');
            }

            return $this->success($resourceCreate);
        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }

    /**
     * Desc:工作认证
     * User: kangshensu@gmail.com
     * Date: 2019-11-17
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function workAuth(Request $request)
    {
        try {
            $params = $request->all();
            $params = array_filter($params);
            $user = auth('api')->user();
            $userId = $user['id'] ?: 1;

            if (empty($params['image'])) {
                throw new \Exception('工作资料不能为空');
            }
            $resourceParams['user_id'] = $userId;
            $resourceParams['source'] = Resources::SOURCE_WORK_IMG;

            $resourceParams['resource'] = json_encode($params['image']);
            $resourceCreate = Resources::query()->create($resourceParams);
            if (!$resourceCreate) {
                throw new \Exception('上传失败');
            }

            //创建用户身份关联审核表
            $userCheckParams = [];
            $userCheckParams['ext_id'] = $resourceCreate->id;
            $userCheckParams['user_id'] = $userId;
            $userCheckParams['status'] = UserCheck::STATUS_TO_AUDIT; //待审核
            $userCheckParams['source'] = UserCheck::SOURCE_WORK_IMG; //身份认证
            $userCheckCreate = UserCheck::query()->create($userCheckParams);
            if (!$userCheckCreate) {
                throw new \Exception('工作审核资料上传失败');
            }

            return $this->success($resourceCreate);

        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }


    /**
     * Desc:认证列表
     * User: kangshensu@gmail.com
     * Date: 2019-11-17
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myAuthInfo(Request $request)
    {
        try {

            $user = auth('api')->user();
            $userId = $user['id'] ?: 1;

            $data = [
                'identity' => [
                    'status' => 0    //待认证
                ],
                'work' => [
                    'status' => 0//待认证
                ]
            ];

            //实名
            $userCheckIdentityObj = UserCheck::query()->where('user_id', $userId)->where('source',
                UserCheck::SOURCE_IDENTITY_IMG)->orderBy('created_at', 'DESC')->first();
            if ($userCheckIdentityObj) {
                $userCheckIdentity = $userCheckIdentityObj->toArray();
                $data['identity']['status'] = $userCheckIdentity['status'];
            }


            //工作
            $userCheckWorkObj = UserCheck::query()->where('user_id', $userId)->where('source',
                UserCheck::SOURCE_WORK_IMG)->orderBy('created_at', 'DESC')->first();
            if ($userCheckWorkObj) {
                $userCheckWork = $userCheckWorkObj->toArray();
                $data['work']['status'] = $userCheckWork['status'];
            }

            return $this->success($data);

        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }

}
