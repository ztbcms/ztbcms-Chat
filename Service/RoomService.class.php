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
     * 获取聊天室ID
     *
     * @param $persons
     * @return array
     */
    static function getRoomIdByPersons($persons){
        $tmp = [];
        foreach($persons as $person){
            $where = [
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id']
            ];
            $tmp[] = D('Chat/RoomPerson')->where($where)->getField('room_id', true);
        }
        $room_ids = $tmp[0];
        foreach($tmp as $v){
            $room_ids = array_intersect($room_ids, $v);
        }
        if($room_ids){
            $room_id = D('Chat/Room')->where(['id' => ['IN', $room_ids], 'person_num' => $person_num])->getField('id');
            if($room_id){
                return self::createReturn(true, $room_id, '获取成功');
            }
        }
        return self::createReturn(false, null, '获取失败');
    }

    /**
     * 创建聊天室
     *
     * @param $persons
     * @return array
     */
    static function createRoom($persons){
        $person_num = count($persons);
        if(!$person_num){
            return self::createReturn(false, null, '创建失败');
        }

        //当需要创建聊天的成员已有聊天室时，返回已有的聊天室
        $res = self::getRoomIdByPersons($persons);
        if($res['status']){
            return self::createReturn(true, $res['data'], '创建成功');
        }

        $time = time();
        $room_id = D('Chat/Room')->add([
            'name' => implode('、', array_column($persons, 'person_name')),
            'pic' => self::getDefaultRoomPic(),
            'last_msg' => '',
            'last_time' => '',
            'last_person_name' => '',
            'person_num' => 0,
            'add_time' => $time
        ]);
        $success = 0;
        foreach($persons as $person){
            $count = D('Chat/RoomPerson')->where([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
            ])->count();
            if($count){
                //过滤已存在的成员
                continue;
            }
            D('Chat/RoomPerson')->add([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
                'person_name' => $person['person_name'],
                'person_pic' => $person['person_pic'],
                'add_time' => $time
            ]);
            $success++;
        }
        D('Chat/Room')->where(['id' => $room_id])->save(['person_num' => $success]);
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
     * 添加聊天成员
     *
     * @param $room_id
     * @param $persons
     * @return array
     */
    static function addRoomPerson($room_id, $persons){
        $person_num = count($persons);
        if(!$person_num){
            return self::createReturn(false, null, '添加失败');
        }

        $time = time();
        $success = 0;
        foreach($persons as $person){
            $count = D('Chat/RoomPerson')->where([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
            ])->count();
            if($count){
                //过滤已存在的成员
                continue;
            }
            $res = D('Chat/RoomPerson')->add([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
                'person_name' => $person['person_name'],
                'person_pic' => $person['person_pic'],
                'add_time' => $time
            ]);
            if($res){
                $success++;
            }
        }
        $res = D('Chat/Room')->where(['id' => $room_id])->save(['person_num' => ['exp', 'person_num+'.$success]]);
        return self::createReturn(true, $res, '添加成功');
    }

    /**
     * 删除聊天成员
     * TODO 管理员权限限制
     *
     * @param $room_id
     * @param $persons
     * @return array
     */
    static function delRoomPerson($room_id, $persons){
        $person_num = count($persons);
        if(!$person_num){
            return self::createReturn(false, null, '删除失败');
        }
        $success = 0;
        foreach($persons as $person){
            $res = D('Chat/RoomPerson')->where([
                'room_id' => $room_id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
            ])->delete();
            if($res){
                $success++;
            }
        }
        $res = D('Chat/Room')->where(['id' => $room_id])->save(['person_num' => ['exp', 'person_num-'.$success]]);
        return self::createReturn(true, $res, '添加成功');
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