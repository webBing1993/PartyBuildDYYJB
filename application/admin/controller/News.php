<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/21
 * Time: 14:41
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPWechat;
use app\admin\model\News as NewsModel;
use think\Config;
/**
 * Class News
 * @package 第一聚焦控制器
 */
class News extends Admin {

    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('News',$map);
        int_to_string($list,array(
            'status' => array(1 =>"已发布",2=>"已发布"),
            'recommend' => array( 1=>"推荐" , 0=>"不推荐")
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 新闻添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $newModel = new NewsModel();
            $info = $newModel->validate('news')->save($data);
            if($info) {
                return $this->success("新增成功",Url('News/index'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
//            $this->default_pic();
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_time'] = time();
            $newModel = new NewsModel();
            $info = $newModel->validate('news')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("News/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
//            $this->default_pic();
            $id = input('id');
            $msg = NewsModel::get($id);
            $this->assign('msg',$msg);
            
            return $this->fetch();
        }
    }

    /**
     * 删除功能
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = NewsModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }
    /*
     * 推送列表
     */
    public function pushlist(){
        if(IS_POST){
            $id = input('id');//主图文id
            //副图文本周内的新闻消息
            //$t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                //'create_time' => array('egt',$t),
                'status' => 1
            );
            $infoes = NewsModel::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = "【最新动态】";
            }
            return $this->success($infoes);
        }else{
            //新闻消息列表
            $map = array(
                'class' => 1,
                'status' => array('egt',-1)
            );
            $list=$this->lists('Push',$map);
            int_to_string($list,array(
//                'type' => array(1=>'企业号推送',2=>'订阅号推送'),
                'status' => array(-1=>'<span style=\'color: red\'>不通过</span>',0=>"<span style='color:#dd0'>待审核</span>",1=>"<span style='color: green'>通过</span>")
            ));
            //数据重组
            foreach($list as $value){
                $msg = NewsModel::where('id',$value['focus_main'])->find();
                $value['title'] = 'aa'.$msg['title'];
                //审核信息
//                $review = PushReview::where('push_id',$value['id'])->find();
//                $value['review_name'] = $review['username'];
//                $value['review_time'] = $review['review_time'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            //$t = $this->week_time();
            $info = array(
                //'create_time' => array('egt',$t),
                'status' => 1
            );
            $infoes = NewsModel::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = "【最新动态】";
            }
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }
    /*
     * 新闻推送
     */
    public function push()
    {
        $data = input('post.');
        $httpUrl = config('http_url');
        $openid = config('openid');
        $Wechat = new TPWechat(Config::get('party'));
        $arr1 = $data['focus_main'];      //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = [];  //副图文id
        $articles = [];
        if ($arr1 == -1) {

            return $this->error('请选择主图文');
        } else {
            //主副图文信息
            array_unshift($arr2, $arr1);

            //先上传素材 media_id
            foreach ($arr2 as $k => $v) {
                $info = NewsModel::where('id', $arr1)->find();
                $info['img'] = get_cover($info['front_cover'])['path'];
                $data = array(
                    "media" => '@.' . $info['img']
                );
                $img = $Wechat->uploadForeverMedia($data, 'thumb');
                $info['thumb_media_id'] = $img['media_id'];
                $id = $info['id'];
                $info['content_source_url'] = "$httpUrl/home/news/detail/id/$id";

                array_push($articles, $info);
            }

            //图文素材列表
            $article = array();
            foreach ($articles as $k => $v) {
                $article['articles'][$k] = [
                    'thumb_media_id' => $v['thumb_media_id'],
                    'author' => $v['publisher'],
                    'title' => "【最新动态】" . $v['title'],
                    'content_source_url' => $v['content_source_url'],
                    "content" => $v['content'],
                    "digest" => $v['title'],
                    "show_cover_pic" => 0,
                ];
            }

        }

        $lists = $article;
        //上传多条图文素材
        $info = $Wechat->uploadForeverArticles($lists);
        $info = json_decode($info);

        // 测试
        if (!empty($openid)) {
            // 预览图文通知
            $notice = array(
                "touser" => $openid,
                "mpnews" => [
                    "media_id" => $info['media_id']
                ],
                "msgtype" => "mpnews"
            );
            $result = $Wechat->previewMassMessage($notice);
        } else {
            //消息群发
//                $send = [
//                    "filter" => [
//                        "is_to_all" =>true
//                    ],
//                    "mpnews" =>[
//                        "media_id" => $info['media_id']
//                    ],
//                    "msgtype" => "mpnews",
//                    "send_ignore_reprint" => 0
//                ];
//                $result = $Wechat ->sendGroupMassMessage($send);
        }

        $result = json_decode($result);

        if ($result['errcode'] == 0) {
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 1;
            $data['status'] = 0;
            //保存到推送列表
            $result = Push::create($data);
            if ($result) {
                return $this->success('发送成功');
            } else {
                return $this->error('发送失败');
            }
        } else {
            return $this->error('发送失败');
        }
    }
}