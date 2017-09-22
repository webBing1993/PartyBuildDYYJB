<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/7/7
 * Time: 10:13
 */

namespace app\admin\validate;


use think\Validate;

class Centraltask extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'pid' => 'require',
        'type' => 'require',
        'title' => 'require',
        'content' => 'require',
        'summary' => 'require',
        'time' => 'require',
        'address' => 'require',
        'telephone' => 'require',
        'publisher' => 'require'
    ];

    protected $message = [
        'front_cover' => '封面图片不能为空',
        'pid' => '所属栏目不能为空',
        'type' => '类型不能为空',
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'summary' => '简介不能为空',
        'name' => '姓名不能为空',
        'time' => '活动时间不能为空',
        'address' => '活动地址不能为空',
        'telephone' => '联系方式不能为空',
        'publisher' => '发布人不能为空'
    ];
    protected $scene = [
        'one' => ['pid','type','title','content','summary','time','address','telephone','publisher'],
        'two' => ['pid','type','front_cover','title','content','publisher'],
    ];
}