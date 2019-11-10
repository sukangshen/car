<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Services;

use App\Models\Resources;
use App\Models\Tags;
use App\User;

class ProfileService
{
    /**
     * Desc:处理列表数据
     * User: kangshensu@gmail.com
     * Date: 2019-09-14
     * @param $profiles
     * @return array
     */
    public static function profileSearch($profiles)
    {
        if (empty($profiles)) {
            return [];
        }

        $resourceId = $profiles ? array_column($profiles, 'resource_id') : [];
        $userId = $profiles ? array_column($profiles, 'user_id') : [];

        $resourceList = Resources::query()->whereIn('id', $resourceId)->get()->toArray();
        $resourceListMap = $resourceList ? array_column($resourceList, null, 'id') : [];
        $userList = User::query()->whereIn('id', $userId)->get()->toArray();
        $userListMap = $userList ? array_column($userList, null, 'id') : [];


        foreach ($profiles as $index => &$item) {
            if (array_key_exists($item['resource_id'], $resourceListMap)) {
                //调整图片
                $images = json_decode($resourceListMap[$item['resource_id']]['resource'], true);
                $item['wechat_img'] = QiniuService::getFilepathByArray($images['wechat_img']);
                $item['self_img'] = QiniuService::getFilepathByArray($images['self_img']);
            } else {
                $item['wechat_img'] = [];
                $item['self_img'] = [];
            }

            if (array_key_exists($item['user_id'], $userListMap)) {
                $item['nickname'] = $userListMap[$item['user_id']]['nickname'];
                $item['headimgurl'] = $userListMap[$item['user_id']]['headimgurl'];
            } else {
                $item['nickname'] = '';
                $item['headimgurl'] = '';
            }

        }
        return $profiles ?: [];
    }


    /**
     * Desc:获取个人详情
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $data
     * @return array|mixed
     */
    public static function profileDetail($data)
    {
        if (empty($data)) {
            return [];
        }
        $resources = [];
        //获取个人信息资源
        $resourcesObj = Resources::query()->where('id', $data['resource_id'])->first();
        if ($resourcesObj) {
            $resources = $resourcesObj->toArray();
        }
        //个人信息
        $userInfoObj = User::query()->where('id', $data['user_id'])->first();
        if ($userInfoObj) {
            $userInfo = $userInfoObj->toArray();
        }
        $data['wechat_img'] = [];
        $data['self_img'] = [];
        $data['tag_list'] = [];
        if (!empty($resources['resource'])) {
            $images = json_decode($resources['resource'], true);
            $data['wechat_img'] = QiniuService::getFilepathByArray($images['wechat_img']);
            $data['self_img'] = QiniuService::getFilepathByArray($images['self_img']);
        }
        $tagIdList = json_decode($data['tag_id'], true);
        if (!empty($tagIdList)) {
            //获取个人标签
            $tagList = Tags::query()->whereIn('id', $tagIdList)->get()->toArray();
            $data['tag_list'] = $tagList ? array_column($tagList, 'name') : [];
        }

        $data['nickname'] = $userInfo['nickname'] ?: '';
        $data['headimgurl'] = $userInfo['headimgurl'] ?: '';

        $data = UtilService::opz($data, ['created_at', 'updated_at', 'deleted_at', 'resource_id', 'end_time']);
        return $data;
    }


    /**
     * Desc:处理我的列表数据
     * User: kangshensu@gmail.com
     * Date: 2019-09-15
     * @param $profiles
     * @return array
     */
    public static function myProfileList($profiles)
    {
        if (empty($profiles)) {
            return [];
        }
        foreach ($profiles as $index => $item) {
            //调整图片
            $images = json_decode($item['resource'], true);
            $profiles[$index]['wechat_img'] = QiniuService::getFilepathByArray($images['wechat_img']);
            $profiles[$index]['self_img'] = QiniuService::getFilepathByArray($images['self_img']);
            unset($profiles[$index]['resource']);

            //判断是否过期
            if ($item['end_time'] > time()) {
                $profiles[$index]['profile_status_name'] = '进行中';
            } else {
                $profiles[$index]['profile_status_name'] = '已失效';
            }
        }

        return $profiles ?: [];
    }


}
