<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\Resources;
use Illuminate\Http\Request;
use App\Http\Services\Qiniu;
use App\Http\Controllers\Api\Controller as Controller;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{

    /**
     * Desc:创建
     * User: kangshensu@gmail.com
     * Date: 2019-09-12
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileCreate(Request $request)
    {
        $params = $request->all();
        $params['end_time'] = time() + 24 * 60 * 60 * Profile::END_TIME;

        //增加资源
        if (empty($params['wechat_img']) || empty($params['self_img'])) {
            return $this->fail(400);
        }
        $user = auth('api')->user();
        error_log('用户信息' . json_encode($user));

        //增加资源
        $resourceParams['user_id'] = $user['id'];
        $resourceImg = ['wechat_img' => $params['wechat_img'], 'self_img' => $params['self_img']];
        $resourceParams['resource'] = json_encode($resourceImg);
        $resourceCreate = Resources::query()->create($resourceParams);
        $params['resource_id'] = $resourceCreate->id;

        $profile = Profile::query()->create($params);
        return $this->success($profile);
    }

    /**
     * Desc:列表
     * User: kangshensu@gmail.com
     * Date: 2019-09-12
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileSearch(Request $request)
    {

        $query = Profile::query();
        $query->leftJoin('resources', 'profile.id', '=', 'resources.id');
        $query->addSelect([
            'profile.id',
            'profile.gender',
            'profile.address',
            'profile.age',
            'profile.height',
            'profile.weight',
            'profile.self_intro',
            'profile.friend_condition',
        ]);
        $query->addSelect('resources.resource');
        $profiles = $query->get();

        return $this->success($profiles);
    }
}
