<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/7/6
 * Time: 16:54
 */

namespace app\admin\controller;

use app\admin\model\Centraltask as CentraltaskModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Centraltask
 * @package app\admin\controller
 * 中心工作
 */
class Centraltask extends Admin {
    /**
     * 主页列表
     */
    public function index() {
        $map = array(
            'status' => array('eq',1),  // 推送  未推送
        );
        $list = $this->lists('Centraltask',$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
            'type' => array(1=>"最新通知",2=>"工作展示"),
            'pid' => array(1=>"美丽村庄",2=>"美丽城镇",3=>"平安维稳",4=>"五水共治",5=>"三改一拆"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    public function add() {
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])) {
                unset($data['id']);
            }
            if(!empty($data['time'])) {
                $data['time'] = strtotime($data['time']);
            }
            $Model = new CentraltaskModel();
            if($data['type'] == 1) {
                $info = $Model->validate('Centraltask.one')->save($data);
            }else {
                $info = $Model->validate('Centraltask.two')->save($data);
            }
            if($info) {
                return $this->success("新增成功",Url('Centraltask/index'));
            }else{
                return $this->get_update_error_msg($Model->getError());
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
    public function edit() {
        if(IS_POST) {
            $data = input('post.');
            $Model = new CentraltaskModel();
            if(!empty($data['time'])) {
                $data['time'] = strtotime($data['time']);
            }
            if($data['type'] == 1) {
                $info = $Model->validate('Centraltask.one')->save($data,['id'=>input('id')]);
            }else {
                $info = $Model->validate('Centraltask.two')->save($data,['id'=>input('id')]);
            }
            if($info){
                return $this->success("修改成功",Url("Centraltask/index"));
            }else{
                return $this->get_update_error_msg($Model->getError());
            }
        }else{
//            $this->default_pic();
            $id = input('id');
            $msg = CentraltaskModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del() {
        $id = input('id');
        $data['status'] = '-1';
        $info = CentraltaskModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }


    /**
     * 推送列表
     */
    public function pushlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            date_default_timezone_set("PRC");        //初始化时区
            $y = date("Y");        //获取当天的年份
            $m = date("m");        //获取当天的月份
            $d = date("d");        //获取当天的号数
            $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
            $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
            //$t为本周周一，$s为上周周一
            switch($time){
                case 1: $t = $todayTime;
                    break;
                case 2: $t = $todayTime - 86400*1;
                    break;
                case 3: $t = $todayTime - 86400*2;
                    break;
                case 4: $t = $todayTime - 86400*3;
                    break;
                case 5: $t = $todayTime - 86400*4;
                    break;
                case 6: $t = $todayTime - 86400*5;
                    break;
                case 7: $t = $todayTime - 86400*6;
                    break;
                default:
            }
            $info = array(
                'id' => array('neq',$id),
//                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = CentraltaskModel::where($info)->select();
            foreach ($infoes as $value) {
                if($value['type'] == 1){
                    $value['type_text'] = "【最新通知】";
                }else {
                    $value['type_text'] = "【工作展示】";
                }
            }
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 6,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = CentraltaskModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本周内的新闻消息
            date_default_timezone_set("PRC");        //初始化时区
            $y = date("Y");        //获取当天的年份
            $m = date("m");        //获取当天的月份
            $d = date("d");        //获取当天的号数
            $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
            $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
            //$t为本周周一，$s为上周周一
            switch($time){
                case 1: $t = $todayTime;
                    break;
                case 2: $t = $todayTime - 86400*1;
                    break;
                case 3: $t = $todayTime - 86400*2;
                    break;
                case 4: $t = $todayTime - 86400*3;
                    break;
                case 5: $t = $todayTime - 86400*4;
                    break;
                case 6: $t = $todayTime - 86400*5;
                    break;
                case 7: $t = $todayTime - 86400*6;
                    break;
                default:
            }
            $info = array(
//                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = CentraltaskModel::where($info)->select();
            foreach ($infoes as $value) {
                if($value['type'] == 1){
                    $value['type_text'] = "【最新通知】";
                }else {
                    $value['type_text'] = "【工作展示】";
                }
            }
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function push()
    {
        $data = input('post.');
        $httpUrl = config('http_url');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if ($arr1 == -1) {
            return $this->error("请选择主图文!");
        } else {
            //主图文信息
            $focus1 = CentraltaskModel::where('id', $arr1)->find();
            $title1 = $focus1['title'];
            if ($focus1['type'] == 1) {
                $type_name1 = "【最新通知】";
                $url1 = $httpUrl."/home/centraltask/note/id/" . $focus1['id'] . ".html";
            } else {
                $type_name1 = "【工作展示】";
                $url1 = $httpUrl."/home/centraltask/work/id/" . $focus1['id'] . ".html";
            }
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1, 0, 100);
            $content1 = str_replace("&nbsp;", "", $des1);  //空格符替换成空
            $img1 = Picture::get($focus1['front_cover']);
            $path1 = $httpUrl. $img1['path'];
            $information1 = array(
                "title" => $type_name1 . $title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if (!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key => $value) {
                $focus = CentraltaskModel::where('id', $value)->find();
                if ($focus['type'] == 1) {
                    $type_name = "【最新通知】";
                    $url = $httpUrl."/home/centraltask/note/id/" . $focus['id'] . ".html";
                } else {
                    $type_name = "【工作展示】";
                    $url = $httpUrl."/home/centraltask/work/id/" . $focus['id'] . ".html";
                }
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str, 0, 100);
                $content = str_replace("&nbsp;", "", $des);  //空格符替换成空
                $img = Picture::get($focus['front_cover']);
                $path = $httpUrl . $img['path'];
                $info = array(
                    "title" => $type_name . $title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k => $v) {
                $information[0] = $information1;
                $information[$k + 1] = $v;
            }
        } else {
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value) {
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('learn'));
        $touser = config('touser');
        $newsConf = config('learn');
        $message = array(
            "touser" => $touser, //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => $newsConf['agentid'],
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0) {
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 6;
            //保存到推送列表
            $s = Push::create($data);
            if ($s) {
                return $this->success("发送成功");
            } else {
                return $this->error("发送失败");
            }
        } else {
            return $this->error("发送失败");
        }
    }
}