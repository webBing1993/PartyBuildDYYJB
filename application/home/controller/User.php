<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\WechatDepartment;
use app\home\model\WechatUser;

/**
 * Class User
 * 用户个人中心
 */
class User extends Base {

    /**
     * 设置头像
     */
    public function setHeader(){
        $userId = session('userId');
        $header = input('header');
        $map = array(
            'header' => $header,
        );
        $info = WechatUser::where('userid',$userId)->update($map);
        if($info){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }


    public function usercenter(){
        //游客判断
        $this ->checkAnonymous();
        $this->anonymous();
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->field('name,department,score,headimgurl')->find();
        $count = WechatUser::where(['score' => ['gt',$user['score']]]) ->count();
        $user['rank'] = $count+1;
        $Department = WechatDepartment::where('id',$user['department'])->field('name')->find();
        $user['department'] = $Department['name'];
        $this->assign('user',$user);
        return $this ->fetch();
    }
}
