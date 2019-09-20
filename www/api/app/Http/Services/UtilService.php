<?php
/**
 * Desc:
 * User: kangshensu@gmail.com
 * Date: 2019-09-14
 */

namespace App\Http\Services;


class UtilService
{
    /**
     * Desc:去掉不需要的参数
     * User: kangshensu@gmail.com
     * Date: 2019-09-14
     * @param $result
     * @param array $keys
     * @return mixed
     */
    public static function opz($result, $keys = array())
    {
        $keys = $keys ?: ['deleted_at','updated_at','created_at'];
        foreach ($keys as $key) {
            if (isset($result[$key])) {
                unset($result[$key]);
            }
        }
        foreach ($result as $k => $val) {
            if (is_array($val)) {
                $result[$k] = self::opz($val, $keys);
            }
        }
        return $result;
    }

    /**
     * Desc:保留需要的参数
     * User: kangshensu@gmail.com
     * Date: 2019-09-14
     * @param $result
     * @param array $keys
     * @return array
     */
    public static function opt($result, $keys = array())
    {
        $ret = array();
        if (empty($result) || empty($keys)) {
            return $ret;
        }

        foreach ($result as $k => $val) {
            if (is_array($val)) {
                $val = self::opt($val, $keys);
                $ret[$k] = $val;
            } else {
                if (in_array($k, $keys)) {
                    $ret[$k] = $result[$k];
                }
            }
        }
        return $ret;
    }
}