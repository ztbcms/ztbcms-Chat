<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 15:47
 */

namespace Chat\Service;

use Chat\Model\MsgModel;
use System\Service\BaseService;

class MsgService extends BaseService{

    /**
     * 发送消息
     *
     * @param $room_id
     * @param array $person 发送人
     * @param $content
     * @param string $content_type
     * @return array
     */
    static function sendMsg($room_id, $person, $content, $content_type = MsgModel::CONTENT_TYPE_TEXT){
        $room_person_id = D('Chat/RoomPerson')->where([
            'room_id' => $room_id,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->getField('id');
        if(!$room_person_id){
            return self::createReturn(false, null, '发送失败');
        }
        $res = D('Chat/Msg')->add([
            'room_id' => $room_id,
            'room_person_id' => $room_person_id,
            'content' => $content,
            'content_type' => $content_type,
            'add_time' => time(),
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id'],
            'person_name' => $person['person_name'],
            'person_pic' => $person['person_pic']
        ]);
        //未读数量+1
        D('Chat/RoomPerson')->where(['room_id' => $room_id, 'id' => ['NEQ', $room_person_id]])->save([
            'no_read_num' => ['exp', '`no_read_num`+1']
        ]);
        return self::createReturn(true, $res, '发送成功');
    }

    /**
     * 获取消息
     *
     * @param $room_id
     * @param array $person 当前获取聊天的用户
     * @param $page
     * @param $limit
     * @param $ignore
     * @return array
     */
    static function getMsgList($room_id, $person, $page = 1, $limit = 20, $ignore = false){
        $room_person_id = D('Chat/RoomPerson')->where([
            'room_id' => $room_id,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->getField('id');
        if($ignore == false && !$room_person_id){
            return self::createReturn(false, null, '获取失败');
        }
        $items = D('Chat/Msg')->where(['room_id' => $room_id])->page($page, $limit)->order('`add_time` DESC')->select();
        $items = array_reverse($items);
        foreach($items as &$item){
            if($room_person_id == $item['room_person_id']){
                $item['is_me'] = 1;
            }else{
                $item['is_me'] = 0;
            }
        }
        $total_items = D('Chat/Msg')->where(['room_id' => $room_id])->count();
        $data = [
            'items' => $items ?: [],
            'page' => $page,
            'limit' => $limit,
            'total_items' => $total_items,
            'total_pages' => ceil($total_items/$limit)
        ];
        //未读数量 => 0
        D('Chat/RoomPerson')->where(['room_id' => $room_id, 'id' => $room_person_id])->save([
            'no_read_num' => 0
        ]);
        return self::createReturn(true, $data, '获取成功');
    }

    /**
     * 获取新消息
     *
     * @param $room_id
     * @param $person
     * @param $last_time
     * @return array
     */
    static function getNewMsg($room_id, $person, $last_time){
        $room_person_id = D('Chat/RoomPerson')->where([
            'room_id' => $room_id,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->getField('id');
        if(!$room_person_id){
            return self::createReturn(false, null, '获取失败');
        }
        $items = D('Chat/Msg')->where(['room_id' => $room_id, 'add_time' => ['GT', $last_time]])->order('`add_time` DESC')->select();
        $items = array_reverse($items);

        //未读数量 => 0
        D('Chat/RoomPerson')->where(['room_id' => $room_id, 'id' => $room_person_id])->save([
            'no_read_num' => 0
        ]);
        foreach($items as &$item){
            if($room_person_id == $item['room_person_id']){
                $item['is_me'] = 1;
            }else{
                $item['is_me'] = 0;
            }
        }
        return self::createReturn(true, $items ?: [], '获取成功');
    }

    /**
     * 群发 (私聊)
     *
     * @param array $person     发送人
     * @param array $to_persons 接收人
     * @param $content
     * @param string $content_type
     * @return array
     */
    static function sendGroupMsg($person, $to_persons, $content, $content_type = MsgModel::CONTENT_TYPE_TEXT){
        if(empty($to_persons)){
            return self::createReturn(false, null, '参数错误');
        }
        $success = 0;
        foreach($to_persons as $to_person){
            $persons = [
                ['person_type' => $person['person_type'], 'person_id' => $person['person_id']],
                ['person_type' => $to_person['person_type'], 'person_id' => $to_person['person_id']],
            ];
            $res = RoomService::createRoom($persons);
            if($res['status']){
                $room_id = $res['data'];
                $res = MsgService::sendMsg($room_id, $person, $content, $content_type);
                if($res['status']){
                    //发送成功数量+1
                    $success++;
                }
            }
        }
        return self::createReturn(true, null, '发送成功：'.$success);
    }
}