<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-11-08
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as Controller;
use App\Http\Services\UtilService;
use App\Models\Tags;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function getTagList(Request $request)
    {
        try {
            //获取一级分类列表
            $firstTagList = Tags::query()->where('parent_id', 0)->select(['id', 'name', 'parent_id'])->get()->toArray();
            if (!$firstTagList) {
                throw new \Exception('获取一级标签失败');
            }
            $firstTagIdList = $firstTagList ? array_column($firstTagList, 'id') : [];

            //获取二级标签列表
            $secondTagList = Tags::query()->whereIn('parent_id', $firstTagIdList)->select([
                'id',
                'name',
                'parent_id'
            ])->get()->toArray();
            if (empty($secondTagList)) {
                throw new \Exception('获取二级标签失败');
            }

            //将一级与二级根据从属关系合并
            $secondTagGroupList = UtilService::arrayGroupByColumnName($secondTagList, 'parent_id');

            foreach ($firstTagList as $index => &$item) {
                if (array_key_exists($item['id'], $secondTagGroupList)) {
                    $item['child_list'] = array_values($secondTagGroupList[$item['id']]);
                }
            }

            return $this->success($firstTagList);
        } catch (\Exception $e) {
            return $this->fail(500, $e->getMessage());
        }

    }
}