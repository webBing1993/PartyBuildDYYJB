<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\home\model\Picture;
use app\home\model\Notice as NoticeModel;
use think\Db;


/**
 * 党建主页
 */
class Index extends Base {
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        $this ->anonymous();
        $uid = session('userId');
        $this ->assign('user',$uid);
        $this->assign('test',0);

        return $this->fetch();
    }

    public function moreDataList()
    {
        $len = input('length');
        $res = Db::field('title,create_time,publisher,id,front_cover,class_type,type')
            ->table('pb_notice')
            ->where('type!=1 and status>=0') // notice type1 没有发布人
            ->union("SELECT title,create_time,publisher,id,front_cover,class_type,type FROM pb_learn")
            ->union("SELECT title,create_time,publisher,id,front_cover,class_type,collect as type FROM pb_news")
            ->union("SELECT title,create_time,publisher,id,front_cover,class_type,type FROM pb_centraltask where status>=0 order by create_time desc limit $len,7")
            ->select();

        foreach ($res as $k=>$v) {
            $img = Picture::get($v['front_cover']);
            $res[$k]['path'] = $img['path'];
            $res[$k]['time'] = date("Y-m-d",$v['create_time']);
        }

        if (empty($res)) {

            return $this->error('数据为空!');
        } else {

            return $this->success('数据获取成功!',null,$res);
        }
    }
}