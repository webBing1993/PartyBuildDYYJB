<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,
//    'log'          => [
//        'type' => 'trace', // 支持 socket trace file
//    ],

    /* 默认模块和控制器 */
    'default_module' => 'home',

    /* URL配置 */
    'base_url'=>'',
    'parse_str'=>[
        '__ROOT__' => '/',
        '__STATIC__' => '/static',
        '__ADMIN__' => '/admin',
        '__HOME__' => '/home',
    ],
    
    /* 公众号配置 */
    'party' => array(
        'login' => 'http://zthm.0571ztnet.com/home/verify/index',
        'token' => 'N3mIjNX',
        'encodingaeskey' => 'RxanruTaFxW7X5r5Cx2xRrI91dhRgNUx77KM3paUfS7',
        'appid' => 'wx89994ef58b069d2b',
        'appsecret' => 'c1a2250667aae7d6898517343b0270f1'
    ),
    

    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',

     /*直播地址*/
    'live_path' => 'https://pullhls36734237.live.126.net/live/4f2e1cac3a694f9ba9c0ed4397c42a2d/playlist.m3u8',

     //  推送网站域名
    'http_url' => "http://ymz.zt.cn",
    // 本地测试 线上请直接为空
    'openid' => 'ome1gxJkdYt9Ji1LZjvl4d2d-6Fk'//王志超'

];
