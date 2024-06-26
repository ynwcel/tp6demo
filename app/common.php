<?php
// 应用公共文件
declare(strict_types=1);

use think\Response;
use think\facade\Env;
use think\facade\Log;
use think\facade\Config;
use think\facade\Request;

if (!function_exists('http_request_post')) {
    /**
     * Notes：post curl
     * {2023/11/13}
     * @param $url
     * @param $requestString
     * @param int $timeout
     * @param string[] $header
     * @return bool|string
     */
    function http_request_post($url, $requestString, $timeout = 5, $header = ["content-type: application/json;charset='utf-8'"], $other = [])
    {
        if ($url == "" || $requestString == "" || $timeout <= 0) {
            return false;
        }
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HTTPHEADER, $header); //模拟的header头
        curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);

        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($con, CURLOPT_SSL_VERIFYHOST, false);

        if (!empty($other)) {
            if (isset($other['basic'])) {
                curl_setopt($con, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            }
            if (isset($other['ssl'])) {
                curl_setopt($con, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($con, CURLOPT_SSL_VERIFYHOST, true);
            }
        }

        $result = curl_exec($con);
        curl_close($con);
        return $result;
    }
}

if (!function_exists('get_tree')) {
    /**
     * 递归无限级分类权限
     * @param array $data
     * @param int $pid
     * @param string $field1 父级字段
     * @param string $field2 子级关联的父级字段
     * @param string $field3 子级键值
     * @return mixed
     */
    function get_tree($data, $pid = 0, $field1 = 'id', $field2 = 'pid', $field3 = 'children')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$field2] == $pid) {
                $v[$field3] = get_tree($data, $v[$field1]);
                $arr[] = $v;
            }
        }
        return $arr;
    }
}

if (!function_exists('is_url')) {
    //是否
    function is_url($url)
    {
        if (preg_match("/^http(s)?:\\/\\/.+/", $url)) {
            return $url;
        }
    }
}

if (!function_exists('delete_dir')) {
    /**
     * 遍历删除文件夹所有内容
     * @param  string $dir 要删除的文件夹
     */
    function delete_dir($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..') {
                $filepath = $dir . '/' . $file;
                if (is_dir($filepath)) {
                    delete_dir($filepath);
                } else {
                    @unlink($filepath);
                }
            }
        }
        closedir($dh);
        @rmdir($dir);
    }
}

if (!function_exists('rand_string')) {
    /**
     * Notes：随机数
     * {2023/11/15}
     * @param string $length
     * @param int $type
     * @return string
     */
    function rand_string($length = '32', $type = 4): string
    {
        $rand = '';
        switch ($type) {
            case '1':
                $randstr = '0123456789';
                break;
            case '2':
                $randstr = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case '3':
                $randstr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                $randstr = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }
        $max = strlen($randstr) - 1;
        mt_srand((float)microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $rand .= $randstr[mt_rand(0, $max)];
        }
        return $rand;
    }
}


/**
 * 在数据列表中搜索
 * @param array $list
 * @param       $condition
 * @return array
 */
function list_search(array $list, array $condition): array
{
    $resultSet = [];
    foreach ($list as $key => $data) {
        $find = false;
        foreach ($condition as $field => $value) {
            $value = (string)$value;
            if (isset($data[$field])) {
                if (str_starts_with($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } else {
                    if ($data[$field] == $value) {
                        $find = true;
                    }
                }
            }
        }
        if ($find) {
            $resultSet[] = &$list[$key];
        }
    }

    return count($resultSet) === 1 ? current($resultSet) : $resultSet;
}

/**
 * 数组转换成Tree树结构
 * @param array $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 */
function list_to_tree(array $list, string $pk = 'id', string $pid = 'pid', string $child = '_child', int $root = 0): array
{
    $tree = $refer = [];
    if (is_array($list)) {
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }

    return $tree;
}

/**
 * 数组排序
 * @param        $list
 * @param        $field
 * @param string $sortby
 * @return array
 */
function list_sort_by(array $list, string $field, string $sortby = 'asc'): array
{
    $refer = $resultSet = [];
    foreach ($list as $i => $data) {
        $refer[$i] = &$data[$field];
    }
    switch ($sortby) {
        case 'asc': // 正向排序
            asort($refer);
            break;
        case 'desc': // 逆向排序
            arsort($refer);
            break;
        case 'nat': // 自然排序
            natcasesort($refer);
            break;
    }
    foreach ($refer as $key => $val) {
        $resultSet[] = &$list[$key];
    }

    return $resultSet;
}

/**
 * 获取所有子ID
 * @param array $list
 * @param string $child
 * @return array
 */
function children_id(array $list, string $child = '_child'): array
{
    $tree = $refer = [];
    if (is_array($list)) {
        foreach ($list as $key => $data) {
            if (!isset($data[$child])) {
                $tree[] = $list[$key]['id'];
            } else {
                $tree = array_merge($tree, children_id($data[$child], $child));
            }
        }
    }

    return $tree;
}
