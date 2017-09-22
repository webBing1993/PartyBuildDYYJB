<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/22
 * Time: 13:53
 */

namespace app\home\model;


use think\Model;

class Centraltask extends Model {

    /**
     * 获取通知
     */
    public function getNote() {
        $map = array(
            'status' => 1,
            'type' => 1,
        );
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(2)->select();
        return $res;
    }

    /**
     * 获取工作动态
     */
    public function getWork() {
        $map = array(
            'status' => 1,
            'type' => 2,
        );
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(7)->select();
        return $res;
    }

    /**
     * 获取标签选中
     */
    public function getClickDetail($pid) {
        $map = array(
            'status' => 1,
            'type' => 1,
        );
        if($pid != 0) {
            $map['pid'] = $pid;
        }
        $order = array("create_time desc");
        $note = $this->where($map)->order($order)->limit(2)->select();
        foreach ($note as $value) {
            $value['time'] = date('Y-m-d',$value['create_time']);
        }

        $map2 = array(
            'status' => 1,
            'type' => 2,
        );
        if($pid != 0) {
            $map2['pid'] = $pid;
        }
        $work = $this->where($map2)->order($order)->limit(7)->select();
        foreach ($work as $value) {
            $img = Picture::get($value['front_cover']);
            $value['src'] = $img['path'];
            $value['time'] = date('Y-m-d',$value['create_time']);
        }
        $data = array(
            'note' => $note,
            'work' => $work
        );
        return $data;
    }

    /**
     * 获取工作动态更多数据
     */
    public function getMoreWork($data) {
        $map = array(
            'status' => 1,
            'type' => 2
        );
        if($data['pid'] != 0) {
            $map['pid'] = $data['pid'];
        }
        $order = array('create_time desc');
        $list = $this->where($map)->order($order)->limit($data['length'],5)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['src'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }

        return $list;
    }

    /**
     * 获取通知列表
     */
    public function getNoteList() {
        $map = array(
            'status' => 1,
            'type' => 1,
        );
        $pid = input('pid');
        if($pid !=0 ) {
            $map['pid'] = $pid;
        }
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(7)->select();
        if($res) {
            foreach ($res as $value) {
                $time = time();
                if($time <= $value['time']) {
                    $value['is'] = 0;
                }else {
                    $value['is'] = 1;
                }
            }
        }
        return $res;
    }

    /**
     * 加载更多通知
     */
    public function getMoreNote($data) {
        $map = array(
            'status' => 1,
            'type' => 1
        );
        if($data['pid'] != 0) {
            $map['pid'] = $data['pid'];
        }
        $order = array('create_time desc');
        $list = $this->where($map)->order($order)->limit($data['length'],5)->select();
        foreach($list as $value){
            $value['create_time'] = date("Y-m-d",$value['create_time']);
            $time = time();
            if($time <= $value['time']) {
                $value['is'] = 0;
            }else {
                $value['is'] = 1;
            }
        }
        return $list;
    }

    /**
     * 获取图片
     */
    public function getFrontCover() {
        //随机封面图
        $a = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n',
            '15'=>'o','16'=>'p','17'=>'q','18'=>'i','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w',);
        $front_pic = array_rand($a,1);
        return $front_pic;
    }
}