<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-12
 */

namespace App\Http\Controllers\Api;

use App\Http\Services\ProfileService;
use App\Http\Services\UtilService;
use App\Models\Profile;
use App\Models\Resources;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller as Controller;

class ProfileController extends Controller
{

    /**
     * Desc:校验帖子
     * User: kangshensu@gmail.com
     * Date: 2019-09-14
     */
    public function profileCreateCheck(Request $request)
    {
        $user = auth('api')->user();
        //判断是否有未下架的帖子
        $exist = Profile::query()->where('end_time', '>', time())->where('user_id', $user['id'])->first();

        return $this->success($exist);
    }

    /**
     * Desc:创建帖子
     * User: kangshensu@gmail.com
     * Date: 2019-09-12
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileCreate(Request $request)
    {
        try {
            $params = $request->all();
            $params = array_filter($params);
            $user = auth('api')->user();

            //增加图片资源
            if (empty($params['wechat_img']) || empty($params['self_img'])) {
                throw new \Exception('个人微信或者本人照片为空');
            }
            //检查标签
            if (empty($params['tag_id'])) {
                throw new \Exception('个性标签为空');
            }
            //创建帖子
            $params['user_id'] = $user['id'];
            $params['nickname'] = trim($user['nickname']);
            $params['address_live'] = trim($params['address_live']);
            $params['address_live_name'] = trim($params['address_live_name']);
            $params['address_birth'] = trim($params['address_birth']);
            $params['address_birth_name'] = trim($params['address_birth_name']);
            $params['end_time'] = time() + 24 * 60 * 60 * Profile::END_TIME;
            $params['birth'] = $params['age'];
            $params['age'] = floor((time() - strtotime($params['age'])) / (60 * 60 * 24 * 365));
            $params['tag_id'] = json_encode($params['tag_id']);
            $params['family_info'] = trim($params['family_info']);

            $resourceParams['user_id'] = $user['id'];
            $resourceParams['source'] = Resources::SOURCE_PROFILE_IMG;
            $resourceImg = [
                'wechat_img' => $params['wechat_img'],
                'self_img' => $params['self_img']
            ];

            $resourceParams['resource'] = json_encode($resourceImg);
            $resourceCreate = Resources::query()->create($resourceParams);
            $params['resource_id'] = $resourceCreate->id;

            $params = UtilService::opz($params, ['self_img', 'wechat_img']);
            $profile = Profile::query()->create($params);
            return $this->success($profile);
        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }
    }

    /**
     * Desc:全部列表
     * User: kangshensu@gmail.com
     * Date: 2019-09-12
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileSearch(Request $request)
    {
        $query = Profile::query();
//        $query->leftJoin('resources', 'profile.resource_id', '=', 'resources.id');
        $query->select([
            'profile.id as profile_id',
            'profile.gender',
            'profile.age',
            'profile.height',
            'profile.weight',
            'profile.self_intro',
            'profile.friend_condition',
            'profile.nickname',
            'profile.address_birth',
            'profile.address_birth_name',
            'profile.address_live',
            'profile.address_live_name',
            'profile.resource_id',
            'profile.user_id',

        ]);
//        $query->addSelect(['resources.resource', 'resources.id as resource_id']);
        $query->orderBy('profile.created_at', 'desc');
        $profiles = $query->paginate($request->input('limit'))->toarray();
        $profiles['data'] = ProfileService::profileSearch($profiles['data']);

        return $this->success($profiles);
    }


    /**
     * Desc:帖子详情
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileDetail(Request $request)
    {
        $profileId = $request->input('profile_id');

        $profile = Profile::query()->where('id', $profileId)->first();
        if ($profile) {
            $profile = $profile->toArray();
            $profile = ProfileService::profileDetail($profile);
        }

        return $this->success($profile ?: []);
    }

    /**
     * Desc:我的帖子列表
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myProfileList(Request $request)
    {
        $user = auth('api')->user();
        $query = Profile::query();
        $query->where('profile.user_id', $user['id']);
        $query->leftJoin('resources', 'profile.resource_id', '=', 'resources.id');
        $query->addSelect([
            'profile.id as profile_id',
            'profile.gender',
            'profile.age',
            'profile.height',
            'profile.weight',
            'profile.self_intro',
            'profile.friend_condition',
            'profile.nickname',
            'profile.address_birth_name',
            'profile.end_time',
        ]);
        $query->addSelect(['resources.resource', 'resources.id as resource_id']);
        $profiles = $query->orderBy('profile.created_at', 'desc')->get()->toarray();

        $profiles = ProfileService::myProfileList($profiles);

        return $this->success($profiles);

    }
}
