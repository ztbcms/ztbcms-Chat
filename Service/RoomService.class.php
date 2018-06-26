<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 15:47
 */

namespace Chat\Service;

use System\Service\BaseService;

class RoomService extends BaseService{

    protected static function getDefaultRoomPic(){
        //TODO 群聊头像
        return '';
    }

    /**
     * 获取聊天列表
     *
     * @param array $where
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     */
    static function getRoomList($where = [], $order = '', $page = 1, $limit = 20){
        return self::select('Chat/Room', $where, $order, $page, $limit);
    }

    /**
     * 获取聊天室
     *
     * @param $room_id
     * @return array
     */
    static function getRoomById($room_id){
        $room = D('Chat/Room')->where(['id' => $room_id])->find();
        if(empty($room_id)){
            return self::createReturn(false, null, '聊天不存在');
        }
        return self::createReturn(true, $room, '获取成功');
    }

    /**
     * 创建聊天
     *
     * @param $persons
     * @return array
     */
    static function createRoom($persons){
        $time = time();
        $room_id = D('Chat/Room')->add([
            'name' => implode('、', array_column($persons, 'person_name')),
            'pic' => self::getDefaultRoomPic(),
            'last_msg' => '',
            'last_time' => '',
            'last_person_name' => '',
            'add_time' => $time
        ]);
        foreach($persons as $person){
            D('Chat/RoomPerson')->add([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
                'person_name' => $person['person_name'],
                'person_pic' => $person['person_pic'],
                'add_time' => $time
            ]);
        }
        return self::createReturn(true, $room_id, '创建成功');
    }

    /**
     * 获取聊天成员
     *
     * @param $room_id
     * @return array
     */
    static function getRoomPerson($room_id){
        $list = D('Chat/RoomPerson')->where(['room_id' => $room_id])->select();
        return self::createReturn(true, $list ?: [], '获取成功');
    }

    /**
     * 刷新聊天成员信息(头像与昵称)
     *
     * @param $room_id
     * @param $persons
     */
    static function refreshRoomPerson($room_id, $persons){
        foreach($persons as $person){
            D('Chat/RoomPerson')->where([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id']
            ])->save([
                'person_name' => $person['person_name'],
                'person_pic' => $person['person_pic']
            ]);
        }
    }
}