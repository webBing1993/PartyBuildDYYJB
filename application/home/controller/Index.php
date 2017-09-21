<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\admin\model\Picture;
use think\Controller;
use app\home\model\News as NewsModel;
use app\home\model\Learn as LearnModel;
use app\home\model\Notice as NoticeModel;
use app\home\model\Pioneer as PioneerModel;


/**
 * 党建主页
 */
class Index extends Base {
    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $this ->anonymous();
        $uid = session('userId');
        $this ->assign('user',$uid);

        $this->assign('test',0);
        return $this->fetch();
    }

}