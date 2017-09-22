<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/7/6
 * Time: 17:21
 */

namespace app\admin\model;


use think\Model;

class Centraltask extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
        'views' => 0,
        'likes' => 0,
        'comments' => 0,
        'time' => 0,
    ];
}