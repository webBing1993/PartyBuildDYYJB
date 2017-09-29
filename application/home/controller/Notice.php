<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\Notice as NoticeModel;
use app\home\model\WechatUser;


/**
 * Class Notice
 * @package 通知公告/支部活动
 */
class Notice extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        
        $userId = session('userId');
        //相关通知 type = 1
        $map1 = array(
            'type' => 1,
            'status' => array('eq',1)
        );
        $list1 = NoticeModel::where($map1)->order('id desc')->limit(2)->select();
        $this->assign('relevant',$list1);

        //会议情况 type = 2
        $map2 = array(
            'type' => 2,
            'status' => array('eq',1)
        );
        $list2 = NoticeModel::where($map2)->order('id desc')->limit(2)->select();
        $this->assign('meet',$list2);

        //党课情况 type = 3
        $map3 = array(
            'type' => 3,
            'status' => array('eq',1)
        );
        $list3 = NoticeModel::where($map3)->order('id desc')->limit(5)->select();
        $this->assign('party',$list3);

        return $this->fetch();
    }

    /**
     * 相关通知
     */
    public function relevant(){
        //判断是否是游客
        $this->anonymous();
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();

        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        //活动基本信息
        $list = $noticeModel::get($id);
        //重组轮播图片
        $list['carousel'] = json_decode($list['carousel_images']);
        $list['user'] = session('userId');
        //分享图片及链接及描述
//        $image = Picture::where('id',$list['front_cover'])->find();
//        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
//        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
//        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        $this->assign('list',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 相关通知列表
     */
    public function relevantlist(){

        $map = array(
            'status' => array('eq',1),
            'type' => 1,
        );
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        foreach ($list as $value) {
            if($value['start_time'] < time()) {
                $value['is'] = 0;
            }else{
                $value['is'] = 1;
            }
        }

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 更多通知
     */
    public function relevantmore(){
        $len = input('length');
        $map = array(
            'type' => 1,
            'status' => array('eq',1),
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            if($value['start_time'] < time()) {
                $value['state'] = 0;
            }else{
                $value['state'] = 1;
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }

    }

    /**
     * 会议情况
     */
    public function meet(){
        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $meet = $noticeModel->get($id);
        $meet['user'] = session('userId');
        //分享图片及链接及描述
//        $image = Picture::where('id',$meet['front_cover'])->find();
//        $meet['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
//        $meet['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
//        $meet['desc'] = str_replace('&nbsp;','',strip_tags($meet['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(4,$id,$userId);
        $meet['is_like'] = $like;
        $meet['images'] = json_decode($meet['images']);
        $this->assign('meet',$meet);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 会议情况列表页面
     */
    public function meetlist(){
        //会议情况 type = 2
        $map = array(
            'status' => array('eq',1),
            'type' => 2,
        );
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        $this->assign('meet',$list);
        return $this->fetch();
    }

    /**
     * 会议更多
     */
    public function meetmore(){
        $len = input('length');
        //会议情况 type = 2
        $map = array(
            'type' => 2,
            'status' => array('eq',1),
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 党课情况
     */
    public function party(){

        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $party = $noticeModel->get($id);
        $party['user'] = session('userId');
        //分享图片及链接及描述
//        $image = Picture::where('id',$party['front_cover'])->find();
//        $party['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
//        $party['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
//        $party['desc'] = str_replace('&nbsp;','',strip_tags($party['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(4,$id,$userId);
        $party['is_like'] = $like;
        $party['images'] = json_decode($party['images']);
        $this->assign('party',$party);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }


    /**
     * 党课加载更多
     */
    public function partymore(){
        $len = input("length");
        $map = array(
            'type' => 3,
            'status' => array('eq',1)
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }

    }
    /**
     * 创意组织生活  加载更多
     */
    public function regularmore() {
        $len = input("length");
        $map = array(
            'type' => 6,
            'status' => array('eq',1)
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }


}