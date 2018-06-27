<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 17:46
 */

namespace Chat\Controller;

use Chat\Model\RoomPersonModel;
use Chat\Service\MsgService;
use Chat\Service\RoomService;
use Common\Controller\AdminBase;

class IndexController extends AdminBase{

    /**
     * 所有聊天列表
     */
    public function index(){
        $this->display();
    }

    /**
     * 获取所有聊天列表
     */
    public function getAllRoomList(){
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $where = I('get.where', []);
        foreach($where as $k => $v){
            if($v === ''){
                unset($where[$k]);
                continue;
            }
            if($k == 'name'){
                $where[$k] = ['LIKE', '%'.$v.'%'];
            }
        }
        $order = 'last_time DESC,add_time DESC';
        $res = RoomService::getRoomList($where, $order, $page, $limit);
        $this->ajaxReturn($res);
    }

    /**
     * 我的聊天列表
     */
    public function myRoom(){
        $this->display();
    }

    /**
     * 获取我的聊天列表
     */
    public function getMyRoomList(){
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $where = I('get.where', []);
        foreach($where as $k => $v){
            if($v === ''){
                unset($where[$k]);
                continue;
            }
            if($k == 'name'){
                $where[$k] = ['LIKE', '%'.$v.'%'];
            }
        }
        $person_type = RoomPersonModel::PERSON_TYPE_ADMIN;
        $person_id = $this->uid;
        $room_ids = D('Chat/RoomPerson')->where(['person_type' => $person_type, 'person_id' => $person_id])->getField('room_id', true);
        if($room_ids){
            $where['id'] = ['IN', $room_ids];
        }else{
            $where['id'] = 0;
        }
        $order = 'last_time DESC,add_time DESC';
        $res = RoomService::getRoomList($where, $order, $page, $limit);
        if($res['status']){
            foreach($res['data']['items'] as &$item){
                $item['no_read_num'] = D('Chat/RoomPerson')->where(['room_id' => $item['id'], 'person_type' => $person_type, 'person_id' => $person_id])->getField('no_read_num');
            }
        }
        $this->ajaxReturn($res);
    }

    /**
     * 聊天记录
     */
    public function chatMsg(){
        $this->display();
    }

    /**
     * 获取聊天记录
     */
    public function getMsgList(){
        $room_id = I('get.room_id');
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $person = [
            'person_type' => RoomPersonModel::PERSON_TYPE_ADMIN,
            'person_id' => $this->uid
        ];
        $res = MsgService::getMsgList($room_id, $person, $page, $limit, true);
        $this->ajaxReturn($res);
    }

    /**
     * 获取新的聊天记录
     */
    public function getNewMsg(){
        $room_id = I('get.room_id');
        $last_time = I('get.last_time');
        $person = [
            'person_type' => RoomPersonModel::PERSON_TYPE_ADMIN,
            'person_id' => $this->uid
        ];
        $res = MsgService::getNewMsg($room_id, $person, $last_time);
        $this->ajaxReturn($res);
    }

    /**
     * 发送
     */
    public function sendMsg(){
        $room_id = I('post.room_id');
        $content = I('post.content');
        $person = D('Chat/RoomPerson')->where([
            'room_id' => $room_id,
            'person_type' => RoomPersonModel::PERSON_TYPE_ADMIN,
            'person_id' => $this->uid
        ])->find();
        $res = MsgService::sendMsg($room_id, $person, $content);
        $this->ajaxReturn($res);
    }
}